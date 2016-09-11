'use strict';
/**
 * @ngdoc function
 * @name prometeoApp.controller:keyCtrl,
 *                              keyDialogCtrl
 * @description
 * # keyCtrl,
 * # keyDialogCtrl
 * Controller of the esignatureApp
 */

myAdmin.controller("keyCtrl", ['PROPERTIES_ADMIN','keyAdminService','$scope','$modal','dialogs','$location','$log','$cookieStore', '$translate',
    function(PROPERTIES_ADMIN,keyAdminService,$scope,$modal,dialogs,$location,$log,$cookieStore,$translate) {

    var keyCtrl = this;

    keyCtrl.initCtrl = function(){
        angular.element('#div-loading').show();
        keyCtrl.userToken = $cookieStore.get('user').token;
        keyAdminService.getKeys(keyCtrl.userToken)
            .then(function(result){
                angular.element('#div-loading').hide();
                if(result.status == 'OK'){
                    keyCtrl.keys = result.data;
                    keyCtrl.totalRecords = result.total_rows;
                }
            }).catch(function(data){
                angular.element('#div-loading').hide();
                if(data.status == "OFF"){
                    $translate('msgSessionExpired').then(function (msg) {
                        dialogs.error('Error', msg);
                    });
                    $scope.$parent.logout(true);
                } else {
                    if(data.status == null){
                        dialogs.error('Error', "null");
                    } else {
                        dialogs.error('Error', data.status);
                    }
                }
            });
    }

    keyCtrl.addKey = function(){
        var dlg = dialogs.create('client/app/modules/esignature/views/dialog-form/form-key.html','keyDialogCtrl',{
            userToken : keyCtrl.userToken,
            action : -1,
            key : {}},'lg');
        dlg.result.then(function(result){
            if(result.status == "OK"){
                keyCtrl.initCtrl();
            }
        },function(){
            if(angular.equals($scope.name,''))
                $scope.name = 'You did not enter in your name!';
        });
    }

    keyCtrl.editKey = function(key){
        var dlg = dialogs.create('client/app/modules/esignature/views/dialog-form/form-key.html','keyDialogCtrl',{
            userToken : keyCtrl.userToken,
            baseURL : $scope.baseURL,
            action : key.id,
            key : key
        },'lg');
        dlg.result.then(function(result){
            if(result.status == "OK"){
                keyCtrl.initCtrl();
            }
        },function(){
            if(angular.equals($scope.name,''))
                $scope.name = 'You did not enter in your name!';
        });
    }

    keyCtrl.delKey = function(key){
        var dataRequest = {
            id : key.id,
            name : key.name
        };
        var dlg = dialogs.confirm(undefined, '¿Está seguro que desea eliminar la Llave?');
        dlg.result.then(function(btn){
            angular.element('#div-loading').show();
            keyAdminService.delKey(dataRequest, keyCtrl.userToken)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == "OK"){
                        keyCtrl.initCtrl();
                        dialogs.notify(undefined, 'Llave eliminada correctamente.');
                    }
                }).catch(function(data){
                    angular.element('#div-loading').hide();
                    if(data.status == "OFF"){
                        $translate('msgSessionExpired').then(function (msg) {
                            dialogs.error('Error', msg);
                        });
                        $scope.$parent.logout(true);
                    } else {
                        if(data.status == null){
                            dialogs.error('Error', "null");
                        } else {
                            dialogs.error('Error', data.status);
                        }
                    }
                });
        },function(btn){

        });
    }

}]);

myAdmin.controller("keyDialogCtrl", function(PROPERTIES_ADMIN,keyAdminService,$scope,$modalInstance,data,$translate,dialogs){

    $scope.action = data.action;
    $scope.userToken = data.userToken;
    $scope.baseURL = data.baseURL;
    $scope.file = {};

    $scope.title = "DATOS DE LA LLAVE";
    $scope.glyphicon = "glyphicon-lock";

    if(Object.keys(data.key).length !== 0){
        $scope.key = {
            name : data.key.name,
            password : data.key.password,
            expiredDate : data.key.expired_date.substr(8,2) + '/' + data.key.expired_date.substr(5,2) + '/' + data.key.expired_date.substr(0,4),
            active : data.key.active == 1 ? true : false
        };
        $scope.dataEmpty = false;
        $scope.active = $scope.key.active;

    } else {
        $scope.key = {
            password : '',
            expiredDate : '',
            active : true
        };
        $scope.dataEmpty = true;
    }

    $scope.cancel = function(){
        $modalInstance.dismiss('Canceled');
    };

    $scope.submitForm = function(){
        if ($scope.keyForm.$valid){
            if($scope.dataEmpty == true && $scope.file.name == undefined){
                $translate('processApplicDialogMsg2').then(function (msg) {
                    dialogs.error('Error', msg);
                });
            } else {
                var dataRequest = {
                    action : $scope.action,
                    password : $scope.key.password,
                    expired_date : $scope.key.expiredDate
                };
                if(data.action !== -1){
                    dataRequest.active = $scope.key.active === true ? 1 : 0;
                    dataRequest.name_file_old = $scope.key.name;
                }

                angular.element('#div-loading').show();
                var file;
                if($scope.file.name == undefined){
                    file = false;
                } else {
                    file = $scope.file;
                }
                keyAdminService.setKey(dataRequest, file, $scope.userToken)
                    .then(function(result){
                        angular.element('#div-loading').hide();
                        if(result.status == "OK"){
                            dialogs.notify(undefined, 'Datos salvados correctamente.');
                            $modalInstance.close(result);
                        } else {
                            dialogs.error('Error', result.status);
                        }
                    }).catch(function(data){
                        angular.element('#div-loading').hide();
                        dialogs.error(data.error);
                    });
            }
        }
    };
});
