'use strict';
/**
 * @ngdoc function
 * @name prometeoApp.controller:invMttoCtrl,
 *
 * @description
 * # invMttoCtrl,
 * Controller of the esignatureApp
 */

myAdmin.controller("tablasCtrl", ['tablasService','$scope','$modal','dialogs','$location','$log','$cookieStore','$translate',
    function(tablasService,$scope,$modal,dialogs,$location,$log,$cookieStore,$translate) {

        var tablasCtrl = this;

        tablasCtrl.currentPage = 1;
        tablasCtrl.pageSize = 500;
        tablasCtrl.totalRecords = 0;
        tablasCtrl.advanceSearch = false;

        tablasCtrl.dataRequest = {
                numero : '', codigo : '', nombre : '', dato1 : '', dato2 : '', dato3 : '', dato4 : '', dato5 : '', dato6 : '', dato7 : '', dato8 : '',
                dato9 : '', dato10 : '', dato11 : '', dato12 : '', dato13 : '', dato14 : '', dato15 : '', start : 0, size : tablasCtrl.pageSize
        };

        tablasCtrl.orderByField = 'numero';
        tablasCtrl.reverseSort = false;

        if($cookieStore.get('user') == undefined){
            $location.path("/");
        } else {
            tablasCtrl.user = $cookieStore.get('user');
        }

        tablasCtrl.initCtrl = function(){
            tablasCtrl.getTablas();
        };

        tablasCtrl.pageChangeHandler = function(newPageNumber){
            tablasCtrl.currentPage = newPageNumber;
            var indexValue = (tablasCtrl.currentPage - 1) * tablasCtrl.pageSize;
            tablasCtrl.dataRequest.start =  indexValue;
            tablasCtrl.getTablas();
        };

        tablasCtrl.resetSearch = function(){
            tablasCtrl.dataRequest = {
                numero : '', codigo : '', nombre : '', dato1 : '', dato2 : '', dato3 : '', dato4 : '', dato5 : '', dato6 : '', dato7 : '', dato8 : '',
                dato9 : '', dato10 : '', dato11 : '', dato12 : '', dato13 : '', dato14 : '', dato15 : '', start : 0, size : tablasCtrl.pageSize
            };

            tablasCtrl.advanceSearch = false;
            tablasCtrl.getTablas();
        };

        tablasCtrl.searchTabla = function(){
            angular.element('#div-loading').show();
            var dlg = dialogs.create('client/app/modules/admin/views/dialog-form/form-buscar-tabla.html','buscarTablaDialogCtrl',{
                searchCriteria : tablasCtrl.dataRequest
            },'md');
            dlg.result.then(function(result){
                tablasCtrl.currentPage = 1;
                var indexValue = (tablasCtrl.currentPage - 1) * tablasCtrl.pageSize;
                tablasCtrl.dataRequest.numero = result.numero;
                tablasCtrl.dataRequest.codigo = result.codigo;
                tablasCtrl.dataRequest.nombre = result.nombre;
                tablasCtrl.dataRequest.start = indexValue;

                if(tablasCtrl.dataRequest.numero != "" || tablasCtrl.dataRequest.codigo != "" || tablasCtrl.dataRequest.nombre != ""){
                    tablasCtrl.advanceSearch = true;
                }
                tablasCtrl.getTablas();
            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        };

        tablasCtrl.getTablas = function(){
            angular.element('#div-loading').show();
            tablasService.getTablas(tablasCtrl.dataRequest, tablasCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == "OK"){
                        tablasCtrl.tablas = [];
                        for(var i in result.data){
                            //result.data[i].subtablas = [];
                           // result.data[i].dato1 = parseInt(result.data[i].dato1);
                            if(result.data[i].codigo == '-'){
                                tablasCtrl.tablas.push(result.data[i]);
                            }
                        }
                        for(var i in tablasCtrl.tablas){
                            tablasCtrl.tablas[i].subtablas = [];
                            for(var j in result.data){
                                if(tablasCtrl.tablas[i].numero == result.data[j].numero && result.data[j].codigo !== '-'){
                                    tablasCtrl.tablas[i].subtablas.push(result.data[j]);
                                }
                            }
                        }
                        //console.log(tablasCtrl.tablas);
                      //  tablasCtrl.tablas = result.data;
                        tablasCtrl.totalRecords = result.total_records;
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
        };

        tablasCtrl.addTabla = function(){
            var dlg = dialogs.create('client/app/modules/admin/views/dialog-form/form-tabla.html','tablaDialogCtrl',{
                userToken : tablasCtrl.user.token, action : -1, user : tablasCtrl.user
            },{size : 'lg'});
            dlg.result.then(function(result){
                if(result.status == "OK"){
                    tablasCtrl.initCtrl();
                }
            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        };

        tablasCtrl.editTabla = function(numero){
            var dlg = dialogs.create('client/app/modules/admin/views/dialog-form/form-tabla.html','tablaDialogCtrl',{
                userToken : tablasCtrl.user.token, action : numero, dataReq : tablasCtrl.dataRequest, user: tablasCtrl.user
            },{size : 'lg'});
            dlg.result.then(function(result){
                if(result.status == "OK"){
                    tablasCtrl.resetSearch();
                }
            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        };

        tablasCtrl.delTabla = function(numero, nombre){
            var dlg = dialogs.confirm('Confirmacion', "Desea eliminar la tabla " + numero + ' (' + nombre + ') ?','md');
            dlg.result.then(function(btn){
                angular.element('#div-loading').show();
                tablasService.delTabla({numero : numero}, tablasCtrl.user.token)
                    .then(function(result){
                        angular.element('#div-loading').hide();
                        dialogs.notify(undefined, result.message);
                        tablasCtrl.initCtrl();
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
            },function(btn){

            });
        };

        $scope.tableRowExpanded = false;
        $scope.tableRowIndexExpandedCurr = "";
        $scope.tableRowIndexExpandedPrev = "";
        $scope.storeIdExpanded = "";
        $scope.styleRowSelected = {
            'font-weight' : 'bold'
        };

        $scope.dayDataCollapseFn = function () {
            $scope.dayDataCollapse = [];
            for (var i = 0; i < tablasCtrl.tablas.length; i += 1) {
                $scope.dayDataCollapse.push(false);
            }
        };

        $scope.selectTableRow = function (index, storeId) {
            console.log(index);
            console.log(storeId);
            if (typeof $scope.dayDataCollapse === 'undefined') {
                $scope.dayDataCollapseFn();
            }

            if ($scope.tableRowExpanded === false && $scope.tableRowIndexExpandedCurr === "" && $scope.storeIdExpanded === "") {
                $scope.tableRowIndexExpandedPrev = "";
                $scope.tableRowExpanded = true;
                $scope.tableRowIndexExpandedCurr = index;
                $scope.storeIdExpanded = storeId;
                $scope.dayDataCollapse[index] = true;
            } else if ($scope.tableRowExpanded === true) {
                if ($scope.tableRowIndexExpandedCurr === index && $scope.storeIdExpanded === storeId) {
                    $scope.tableRowExpanded = false;
                    $scope.tableRowIndexExpandedCurr = "";
                    $scope.storeIdExpanded = "";
                    $scope.dayDataCollapse[index] = false;
                } else {
                    $scope.tableRowIndexExpandedPrev = $scope.tableRowIndexExpandedCurr;
                    $scope.tableRowIndexExpandedCurr = index;
                    $scope.storeIdExpanded = storeId;
                    $scope.dayDataCollapse[$scope.tableRowIndexExpandedPrev] = false;
                    $scope.dayDataCollapse[$scope.tableRowIndexExpandedCurr] = true;
                }
            }
        };


}]);

myAdmin.controller("tablaDialogCtrl", function(tablasService,$scope,$modalInstance,data,$translate,dialogs){

    $scope.userToken = data.userToken;
    $scope.action = data.action;
    $scope.user = data.user;
    var dataReq1 = data.dataReq;

    $scope.glyphicon = "glyphicon-lock";
    $scope.tablas = [];

    if($scope.action !== -1){
        angular.element('#div-loading').show();
        tablasService.getTabla({numero : $scope.action}, $scope.userToken)
            .then(function(result){
                angular.element('#div-loading').hide();
                if(result.status == 'OK'){
                    for(var i in result.data){
                        result.data[i].subtablas = [];
                        result.data[i].dato1 = parseInt(result.data[i].dato1);
                        if(result.data[i].codigo == '-'){
                            $scope.tablas.push(result.data[i]);
                        } else {
                            $scope.tablas[0].subtablas.push(result.data[i]);
                        }
                    }
                } else {
                    dialogs.error('Error', 'No se recuperaron los datos.');
                    $modalInstance.dismiss('Canceled');
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

    $scope.addTabla = function(){
        var valorIndex = 0;
        if($scope.tablas.length > 0){
            valorIndex = $scope.tablas.length;
        }
        $scope.tablas.push({
            index : valorIndex,
            numero : '',
            codigo : '',
            nombre : '',
            dato1 : '',
            dato2 : '',
            dato3 : '',
            dato4 : '',
            dato5 : '',
            dato6 : '',
            dato7 : '',
            dato8 : '',
            dato9 : '',
            dato10 : '',
            dato11 : '',
            dato12 : '',
            dato13 : '',
            dato14 : '',
            dato15 : '',
            subtablas: []
        });
    };

    $scope.addSubTabla = function(index, numero){
        $scope.tablas[index].subtablas.push({
            numero : numero,
            codigo : '',
            nombre : '',
            dato1 : 0,
            dato2 : '0.00',
            dato3 : '0.00',
            dato4 : '',
            dato5 : '',
            dato6 : '',
            dato7 : '',
            dato8 : '',
            dato9 : '',
            dato10 : '',
            dato11 : '',
            dato12 : '',
            dato13 : '',
            dato14 : '',
            dato15 : '',
        });
    };

    $scope.delTabla = function(index){
        var dlg = dialogs.confirm('Confirmacion', "Desea eliminar la tabla?");
        dlg.result.then(function(btn){
            $scope.tablas.splice(index,1);
        },function(btn){

        });
    };

    $scope.delSubTabla = function(index, tablaIndex){
        var dlg = dialogs.confirm('Confirmacion', "Desea eliminar la subtabla?");
        dlg.result.then(function(btn){
            for(var i in $scope.tablas){
                if($scope.tablas[i].index == tablaIndex){
                    $scope.tablas[i].subtablas.splice(index,1);
                }
            }
        },function(btn){

        });
    };

    $scope.cancel = function(){
        $modalInstance.dismiss('Canceled');
    };

    $scope.submitForm = function(){
        if ($scope.tablaForm.$valid){
            var dataRequest = {
                action : $scope.action,
                tablas : $scope.tablas,
                userId : $scope.user.userId
            };
            angular.element('#div-loading').show();
            tablasService.setTablas(dataRequest, $scope.userToken)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == "OK"){
                        dialogs.notify(undefined, 'Datos salvados correctamente.');
                        result.dataReq = data.dataReq;
                        $modalInstance.close(result);
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
                            dialogs.error('Error', data.message);
                        }
                    }
                });
        }
    };

});

myAdmin.controller("buscarTablaDialogCtrl",function($scope,$modalInstance,data){
    angular.element('#div-loading').hide();
    $scope.searchCriteria = {
        numero : data.searchCriteria.numero,
        codigo : data.searchCriteria.codigo,
        nombre : data.searchCriteria.nombre
    };

    $scope.cancel = function(){
        $modalInstance.dismiss('Canceled');
    };

    $scope.submitForm = function(){
        $modalInstance.close($scope.searchCriteria);
    };
});
