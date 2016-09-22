'use strict';
/**
 * @ngdoc function
 * @name prometeoApp.controller:invMttoCtrl,
 *
 * @description
 * # invMttoCtrl,
 * Controller of the esignatureApp
 */

myInventario.controller("invMttoCtrl", ['PROPERTIES','invMttoService','$scope','$modal','dialogs','$location','$log','$cookieStore',
    function(PROPERTIES,invMttoService,$scope,$modal,dialogs,$location,$log,$cookieStore) {

        var invMttoCtrl = this;

        invMttoCtrl.currentPage = 1;
        invMttoCtrl.pageSize = 50;
        invMttoCtrl.totalRecords = 0;
        invMttoCtrl.advanceSearch = false;

        invMttoCtrl.dataRequest = {
            codigo: '',
            nombre: '',
            start: 0,
            size: invMttoCtrl.pageSize
        };

        invMttoCtrl.orderByField = 'numero';
        invMttoCtrl.reverseSort = false;

        if($cookieStore.get('user') == undefined){
            $location.path("/");
        } else {
            invMttoCtrl.user = $cookieStore.get('user');
        }

        invMttoCtrl.initCtrl = function(){
            invMttoCtrl.getCatalogos();
            invMttoCtrl.getProductos();
        };

        invMttoCtrl.pageChangeHandler = function(newPageNumber){
            invMttoCtrl.currentPage = newPageNumber;
            var indexValue = (invMttoCtrl.currentPage - 1) * invMttoCtrl.pageSize;
            invMttoCtrl.dataRequest.start =  indexValue;
            invMttoCtrl.getProductos();
        };

        invMttoCtrl.resetSearch = function(){
            invMttoCtrl.dataRequest = {
                codigo: '',
                nombre: '',
                start: 0,
                size: invMttoCtrl.pageSize
            };

            invMttoCtrl.advanceSearch = false;
            invMttoCtrl.getProductos();
        };

        invMttoCtrl.searchProducto = function(){
            angular.element('#div-loading').show();
            var dlg = dialogs.create('modules/inventario/views/dialog-form/form-buscar-producto.html','buscarProductoDialogCtrl',{
                countrys : invMttoCtrl.countrys, searchCriteria : invMttoCtrl.dataRequest
            },'lg');
            dlg.result.then(function(result){
                invMttoCtrl.currentPage = 1;
                var indexValue = (invMttoCtrl.currentPage - 1) * invMttoCtrl.pageSize;
                invMttoCtrl.dataRequest.pid = result.pid;
                if(result.perfil != ""){
                    tablasCtrl.dataRequest.perfil = result.perfil.id;
                } else {
                    tablasCtrl.dataRequest.perfil = result.perfil;
                }
                tablasCtrl.dataRequest.nombre = result.nombre;
                tablasCtrl.dataRequest.apellido = result.apellido;
                if(result.paisNacimiento != ""){
                    tablasCtrl.dataRequest.paisNacimiento = result.paisNacimiento.id;
                } else {
                    tablasCtrl.dataRequest.paisNacimiento = result.paisNacimiento;
                }
                if(result.paisResidencia != ""){
                    tablasCtrl.dataRequest.paisResidencia = result.paisResidencia.id;
                } else {
                    tablasCtrl.dataRequest.paisResidencia = result.paisResidencia;
                }
                tablasCtrl.dataRequest.inicio = indexValue;

                if(tablasCtrl.dataRequest.pid != "" || tablasCtrl.dataRequest.perfil != "" || tablasCtrl.dataRequest.nombre != "" ||
                    tablasCtrl.dataRequest.apellido != "" || tablasCtrl.dataRequest.paisNacimiento != "" || tablasCtrl.dataRequest.paisResidencia != "" ||
                    tablasCtrl.dataRequest.proceso != ""){
                    tablasCtrl.advanceSearch = true;
                }
                tablasCtrl.getTablas();

            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        };

        invMttoCtrl.getProductos = function(){
            angular.element('#div-loading').show();
            invMttoService.getProductos(invMttoCtrl.dataRequest, invMttoCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == "OK"){
                        invMttoCtrl.productos = result.data;
                        invMttoCtrl.totalRecords = result.total_records;
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

        invMttoCtrl.addProducto = function(){
            var dlg = dialogs.create('client/app/modules/inventario/views/dialog-form/form-producto.html','productoDialogCtrl',{
                action : -1, user : invMttoCtrl.user, catalogos : invMttoCtrl.catalogos
            },{size : 'md'});
            dlg.result.then(function(result){
                if(result.status == "OK"){
                    invMttoCtrl.initCtrl();
                }
            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        };

        invMttoCtrl.editProducto = function(numero){
            var dlg = dialogs.create('client/app/modules/inventario/views/dialog-form/form-producto.html','productoDialogCtrl',{
                action : numero, dataReq : invMttoCtrl.dataRequest, user: invMttoCtrl.user, catalogos : invMttoCtrl.catalogos
            },{size : 'md'});
            dlg.result.then(function(result){
                if(result.status == "OK"){
                    invMttoCtrl.resetSearch();
                }
            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        };

        invMttoCtrl.delProducto = function(numero, nombre){
            var dlg = dialogs.confirm('Confirmacion', "Desea eliminar la tabla " + numero + ' (' + nombre + ') ?','md');
            dlg.result.then(function(btn){
                angular.element('#div-loading').show();
                invMttoService.delProducto({numero : numero}, tablasCtrl.user.token)
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

        invMttoCtrl.getCatalogos = function(){
            var tablas = [
                PROPERTIES.maetroTablas.categoriasProducto,
                PROPERTIES.maetroTablas.tiposProducto,
                PROPERTIES.maetroTablas.marcasProducto,
                PROPERTIES.maetroTablas.modelosProducto,
                PROPERTIES.maetroTablas.unidadesMedida,
                PROPERTIES.maetroTablas.ivas,
                PROPERTIES.maetroTablas.estadosProducto,
                PROPERTIES.maetroTablas.icesCompra,
                PROPERTIES.maetroTablas.icesVenta
            ];
            invMttoCtrl.catalogos = {
                categoriasProducto : '', tiposProducto : '', marcasProducto : '', modelosProducto : '', unidadesMedida : '',
                ivas : '', estadosProducto : '', icesCompra : '', icesVenta : ''
            };

            invMttoService.getCatalogos(tablas, invMttoCtrl.user.token)
                .then(function(result){
                    for(var i in result.data){
                        for(var j in tablas){
                            if(tablas[j] ==  result.data[i][0].numero){
                                switch(j){
                                    case '0':
                                        invMttoCtrl.catalogos.categoriasProducto = result.data[i];
                                        break;
                                    case '1':
                                        invMttoCtrl.catalogos.tiposProducto = result.data[i];
                                        break;
                                    case '2':
                                        invMttoCtrl.catalogos.marcasProducto = result.data[i];
                                        break;
                                    case '3':
                                        invMttoCtrl.catalogos.modelosProducto = result.data[i];
                                        break;
                                    case '4':
                                        invMttoCtrl.catalogos.unidadesMedida = result.data[i];
                                        break;
                                    case '5':
                                        invMttoCtrl.catalogos.ivas = result.data[i];
                                        break;
                                    case '6':
                                        invMttoCtrl.catalogos.estadosProducto = result.data[i];
                                        break;
                                    case '7':
                                        invMttoCtrl.catalogos.icesCompra = result.data[i];
                                        break;
                                    case '8':
                                        invMttoCtrl.catalogos.icesVenta = result.data[i];
                                        break;
                                }
                            }
                        }
                    }
                }).catch(function(data){
                    dialogs.error('Error', data.message);
                });
        };


}]);

myInventario.controller("productoDialogCtrl", function(tablasService,invMttoService,$scope,$modalInstance,data,$translate,dialogs){

    $scope.catalogos = data.catalogos;
    $scope.action = data.action;
    $scope.user = data.user;
    var dataReq1 = data.dataReq;

    $scope.glyphicon = "glyphicon-lock";
    $scope.tablas = [];

    if($scope.action !== -1){
        angular.element('#div-loading').show();
        dataReq1[0].numero = $scope.action;
        dataReq1[13].start = '';
        dataReq1[14].size = '';
        tablasService.getTablas(dataReq1, $scope.user.userToken)
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
            dato10 : ''
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
