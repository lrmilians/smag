'use strict';
/**
 * @ngdoc function
 * @name prometeoApp.controller:billCtrl,
 *                              billDialogCtrl
 * @description
 * # keyCtrl,
 * # keyDialogCtrl
 * Controller of the esignatureApp
 */

myAdmin.controller("billCtrl", ['catalogsEsignatureService','billAdminService','$scope','$modal','dialogs','$location','$log','$cookieStore', '$translate',
    function(catalogsEsignatureService,billAdminService,$scope,$modal,dialogs,$location,$log,$cookieStore,$translate) {

        var billCtrl = this;
        billCtrl.currentPage = 1;
        billCtrl.pageSize = 15;
        billCtrl.totalRecords = 0;
        billCtrl.advanceSearch = false;

       // billCtrl.q = '';

        billCtrl.dataRequest = {
            name : "",
            num_doc : "",
            processed : "",
            state : "",
            message : "",
            created : "",
            modified : "",
            code : "",
            client_iden : "",
            client_lname : "",
            start : 0,
            size : billCtrl.pageSize
        };


        /*$scope.scrollStyle = $scope.scrollStyle2 = {};
        $scope.scrollTotalRecords = $scope.scrollTotalRecords2 = 8;
        $scope.scrollHeight = $scope.scrollHeight2 = 200;
        $scope.totalRecord = $scope.totalRecord2 = 0;*/

        billCtrl.orderByField = 'id';
        billCtrl.reverseSort = false;

        if($cookieStore.get('user') == undefined){
            $location.path("/");
        } else {
            billCtrl.user = $cookieStore.get('user');
        }
        if(billCtrl.user.rolCode == 'CL01'){
            billCtrl.dataRequest.user_id = billCtrl.user.userId;
        }

        billCtrl.initCtrl = function(){
            var dataRequest = {start : '', size : ''};
            catalogsEsignatureService.getSRIValidations(billCtrl.user.token)
                .then(function(result){
                    if(result.status == 'OK'){
                        billCtrl.SRIValidations = result.data;
                        var indexValue = (billCtrl.currentPage - 1) * billCtrl.pageSize;
                        billCtrl.dataRequest.start = indexValue;
                        billCtrl.getBills(billCtrl.dataRequest);
                    } else {
                        dialogs.error('Error', result.message);
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

        billCtrl.pageChangeHandler = function(newPageNumber){
            billCtrl.currentPage = newPageNumber;
            var indexValue = (billCtrl.currentPage - 1) * billCtrl.pageSize;
            billCtrl.dataRequest.start =  indexValue;
            billCtrl.getBills(billCtrl.dataRequest);
        }

        billCtrl.resetSearch = function(){
            billCtrl.dataRequest = {
                name : "",
                num_doc : "",
                processed : "",
                state : "",
                message : "",
                created : "",
                modified : "",
                code : "",
                client_iden : "",
                client_lname : "",
                start : 0,
                size : billCtrl.pageSize
            }
            if(billCtrl.user.rolCode == 'CL01'){
                billCtrl.dataRequest.user_id = billCtrl.user.userId;
            }
            billCtrl.advanceSearch = false;
            billCtrl.getBills(billCtrl.dataRequest);
        }

        billCtrl.searchBill = function(){
            angular.element('#div-loading').show();
            var dlg = dialogs.create('modules/admin/views/dialog-form/form-search-application.html','searchApplicDialogCtrl',{
                countrys : billCtrl.countrys, searchCriteria : billCtrl.dataRequest
            },'lg');
            dlg.result.then(function(result){
                billCtrl.currentPage = 1;
                var indexValue = (billCtrl.currentPage - 1) * billCtrl.pageSize;
                billCtrl.dataRequest.pid = result.pid;
                if(result.perfil != ""){
                    billCtrl.dataRequest.perfil = result.perfil.id;
                } else {
                    billCtrl.dataRequest.perfil = result.perfil;
                }
                billCtrl.dataRequest.nombre = result.nombre;
                billCtrl.dataRequest.apellido = result.apellido;
                if(result.paisNacimiento != ""){
                    billCtrl.dataRequest.paisNacimiento = result.paisNacimiento.id;
                } else {
                    billCtrl.dataRequest.paisNacimiento = result.paisNacimiento;
                }
                if(result.paisResidencia != ""){
                    billCtrl.dataRequest.paisResidencia = result.paisResidencia.id;
                } else {
                    billCtrl.dataRequest.paisResidencia = result.paisResidencia;
                }
                billCtrl.dataRequest.inicio = indexValue;

                if(billCtrl.dataRequest.pid != "" || billCtrl.dataRequest.perfil != "" || billCtrl.dataRequest.nombre != "" ||
                    billCtrl.dataRequest.apellido != "" || billCtrl.dataRequest.paisNacimiento != "" || billCtrl.dataRequest.paisResidencia != "" ||
                    billCtrl.dataRequest.proceso != ""){
                    billCtrl.advanceSearch = true;
                }
                billCtrl.getApplications(billCtrl.dataRequest);

            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        }

        billCtrl.getBills = function(dataRequest){
            angular.element('#div-loading').show();
            billAdminService.getBills(dataRequest, billCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == "OK"){
                        billCtrl.bills = result.data;
                        billCtrl.totalRecords = result.total_rows;
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
                            dialogs.error('Error', data.message);
                        }
                    }
                });
        }

        billCtrl.addBill = function(){
            var dlg = dialogs.create('client/app/modules/esignature/views/dialog-form/form-add-doc.html','addDocDialogCtrl',{
                userToken : billCtrl.user.token,
                docType : 'FACTURA',
                codDoc : '01'},'lg');
            dlg.result.then(function(result){
                if(result.status == "OK"){
                   billCtrl.initCtrl();
                }
            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        }

        billCtrl.processBill = function(bill){
            var dataRequest = {
                doc_type : 'facturas',
                name : bill.name,
                id : bill.id,
            };
            angular.element('#div-loading').show();
            billAdminService.processBill(dataRequest, billCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == "OK"){
                        dialogs.notify(undefined, 'Proceso terminado correctamente. Factura ' + result.data.authorize_result.estado);
                        billCtrl.initCtrl();
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
                            dialogs.error('Error', data.message);
                        }
                    }
                });
        }


        billCtrl.viewUser = function(user){
            var dlg = dialogs.create('client/app/modules/admin/views/dialog-view/view-data-template.html','userDialogCtrl',{
                userToken : billCtrl.user.token,
                userNameLogin : billCtrl.user.username,
                roles : billCtrl.roles,
                action : user.id,
                user : user
            },'lg');
            dlg.result.then(function(result){
                if(result.status == "OK"){
                    billCtrl.initCtrl();
                }
            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        }

}]);

myAdmin.controller("addDocDialogCtrl", function(esignAdminService,$scope,$modalInstance,data,$translate,dialogs){

    $scope.userToken = data.userToken;
    $scope.docType = data.docType;
    $scope.file = {};

    $scope.glyphicon = "glyphicon-lock";

    $scope.cancel = function(){
        $modalInstance.dismiss('Canceled');
    };

    $scope.submitForm = function(){
        if ($scope.docForm.$valid){
            if($scope.dataEmpty == true && $scope.file.name == undefined){
                $translate('processApplicDialogMsg2').then(function (msg) {
                    dialogs.error('Error', msg);
                });
            } else {
                var dataRequest = {
                    name : $scope.file.name,
                    cod_doc : data.codDoc,
                    doc_type : data.docType
                };
                angular.element('#div-loading').show();
                esignAdminService.addDoc(dataRequest, $scope.file, $scope.userToken)
                    .then(function(result){
                        angular.element('#div-loading').hide();
                        if(result.status == "OK"){
                            dialogs.notify(undefined, 'Datos salvados correctamente.');
                            $modalInstance.close(result);
                        } else {
                            dialogs.error('Error', result.data);
                        }
                    }).catch(function(data){
                        angular.element('#div-loading').hide();
                        dialogs.error('Error', data.message + ' ' + data.data);
                    });
            }
        }
    };
});
