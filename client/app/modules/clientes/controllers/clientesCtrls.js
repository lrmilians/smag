'use strict';
/**
 * @ngdoc function
 * @name prometeoApp.controller:clientesCtrl,
 *
 * @description
 * # clientesCtrl,
 * Controller of the esignatureApp
 */

myClientes.controller("clientesCtrl", ['PROPERTIES','clientesService','$scope','$modal','dialogs','$location','$log','$cookieStore','$translate',
    function(PROPERTIES,clientesService,$scope,$modal,dialogs,$location,$log,$cookieStore,$translate) {

        var clientesCtrl = this;

        clientesCtrl.currentPage = 1;
        clientesCtrl.pageSize = 50;
        clientesCtrl.totalRecords = 0;
        clientesCtrl.advanceSearch = false;

        clientesCtrl.dataRequest = {
            codigo: '',
            identificacion: '',
            razon_social: '',
            email: '',
            start: 0,
            size: clientesCtrl.pageSize,
            catalogos : [
                PROPERTIES.maetroTablas.tiposIdentificacion,
                PROPERTIES.maetroTablas.condicionesPago
            ]
        };

        clientesCtrl.orderByField = 'numero';
        clientesCtrl.reverseSort = false;

        if($cookieStore.get('user') == undefined){
            $scope.$parent.logout(true);
            $location.path("/");
        } else {
            clientesCtrl.user = $cookieStore.get('user');
        }

        clientesCtrl.initCtrl = function(){
            if(clientesCtrl.user !== undefined){
                clientesCtrl.getClientes();
            }
        };

        clientesCtrl.pageChangeHandler = function(newPageNumber){
            clientesCtrl.currentPage = newPageNumber;
            var indexValue = (clientesCtrl.currentPage - 1) * clientesCtrl.pageSize;
            clientesCtrl.dataRequest.start =  indexValue;
            clientesCtrl.getClientes();
        };

        clientesCtrl.resetSearch = function(){
            clientesCtrl.dataRequest = {
                codigo: '',
                identificacion: '',
                razon_social: '',
                email: '',
                start: 0,
                size: clientesCtrl.pageSize,
                catalogos : [
                    PROPERTIES.maetroTablas.tiposIdentificacion,
                    PROPERTIES.maetroTablas.condicionesPago
                ]
            };

            clientesCtrl.advanceSearch = false;
            clientesCtrl.getClientes();
        };

        clientesCtrl.searchCliente = function(){
            angular.element('#div-loading').show();
            var dlg = dialogs.create('client/app/modules/clientes/views/dialog-form/form-buscar-cliente.html','buscarClienteDialogCtrl',{
                searchCriteria : clientesCtrl.dataRequest
            },'lg');
            dlg.result.then(function(result){
                clientesCtrl.currentPage = 1;
                var indexValue = (clientesCtrl.currentPage - 1) * clientesCtrl.pageSize;
                clientesCtrl.dataRequest.codigo = result.codigo;
                clientesCtrl.dataRequest.identificacion = result.identificacion;
                clientesCtrl.dataRequest.razon_social = result.razon_social;
                clientesCtrl.dataRequest.email = result.email;
                clientesCtrl.dataRequest.inicio = indexValue;

                if(clientesCtrl.dataRequest.codigo != "" || clientesCtrl.dataRequest.identificacion != "" || clientesCtrl.dataRequest.razon_social != ""
                    || clientesCtrl.dataRequest.email != ""){
                    clientesCtrl.advanceSearch = true;
                }
                clientesCtrl.getClientes();

            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        };

        clientesCtrl.getClientes = function(){
            angular.element('#div-loading').show();
            clientesService.getClientes(clientesCtrl.dataRequest, clientesCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == "OK"){
                        clientesCtrl.clientes = result.data;
                        clientesCtrl.totalRecords = result.total_records;
                        clientesCtrl.catalogos = {
                            tiposIdentificacion : '', condicionesPago : ''
                        };
                        for(var i in result.catalogos){
                            for(var j in clientesCtrl.dataRequest.catalogos){
                                if(i == clientesCtrl.dataRequest.catalogos[j]){
                                    switch(j){
                                        case '0':
                                            clientesCtrl.catalogos.tiposIdentificacion = result.catalogos[i];
                                            break;
                                        case '1':
                                            clientesCtrl.catalogos.condicionesPago = result.catalogos[i];
                                            break;
                                    }
                                }
                            }
                        }
                        for(var i in clientesCtrl.clientes){
                            if(clientesCtrl.clientes[i].parte_relacionada === 't'){
                                clientesCtrl.clientes[i].parte_relacionada = true;
                            } else {
                                clientesCtrl.clientes[i].parte_relacionada = false;
                            }
                            /*for(var j in clientesCtrl.catalogos.estadosProducto){
                                if(clientesCtrl.productos[i].estado == clientesCtrl.catalogos.estadosProducto[j].codigo){
                                    clientesCtrl.productos[i].estadoNombre = clientesCtrl.catalogos.estadosProducto[j].nombre;
                                }
                            }*/
                        }
                    } else {
                        dialogs.error('Error', result.message);
                    }
                }).catch(function(data){
                    angular.element('#div-loading').hide();
                    if(data.status == "OFF"){
                        $translate('msgSessionExpired').then(function (msg) {
                            dialogs.error('Error', data.message);
                        });
                        $scope.$parent.logout(true);
                    } else {
                        dialogs.error('Error', data.message);
                    }
                });
        };

        clientesCtrl.addCliente = function(){
            var dlg = dialogs.create('client/app/modules/clientes/views/dialog-form/form-cliente.html','clienteDialogCtrl',{
                action : -1, user : clientesCtrl.user, catalogos : clientesCtrl.catalogos
            },{size : 'md'});
            dlg.result.then(function(result){
                if(result.status == "OK"){
                    clientesCtrl.initCtrl();
                } else {
                    if(result.status == 'OFF'){
                        $translate('msgSessionExpired').then(function (msg) {
                            dialogs.error('Error', msg);
                        });
                        $scope.$parent.logout(true);
                    }
                }
            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        };

        clientesCtrl.editCliente = function(cliente){
            var dlg = dialogs.create('client/app/modules/clientes/views/dialog-form/form-cliente.html','clienteDialogCtrl',{
                action : cliente.id, user: clientesCtrl.user, catalogos : clientesCtrl.catalogos, cliente : cliente
            },{size : 'md'});
            dlg.result.then(function(result){
                if(result.status == "OK"){
                    clientesCtrl.initCtrl();
                } else {
                    if(result.status == 'OFF'){
                        $translate('msgSessionExpired').then(function (msg) {
                            dialogs.error('Error', msg);
                        });
                        $scope.$parent.logout(true);
                    }
                }
            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        };

}]);

myInventario.controller("clienteDialogCtrl", function(PROPERTIES,clientesService,$scope,$modalInstance,data,$translate,dialogs){
    $scope.catalogos = data.catalogos;
    $scope.action = data.action;
    $scope.user = data.user;

    $scope.glyphicon = "glyphicon-lock";
    $scope.tablas = [];

    $scope.patternCodigo = PROPERTIES.expresionesRegulares.codigoCliente;
    $scope.patternRazonSocial = PROPERTIES.expresionesRegulares.razonSocialCliente;

    $scope.patternPrecioVenta = PROPERTIES.expresionesRegulares.decimal186;
    $scope.patternReferencia = PROPERTIES.expresionesRegulares.referenciaProducto;
    $scope.patternDescripcion = PROPERTIES.expresionesRegulares.descripcionProducto;
    $scope.patternStockActual = $scope.patternStockMinimo = $scope.patternStockMaximo = PROPERTIES.expresionesRegulares.decimal126;
    $scope.patternUbicacion = PROPERTIES.expresionesRegulares.texto;
    $scope.patternCostoUltimaCompra = $scope.patternCostoPrimeraCompra = PROPERTIES.expresionesRegulares.decimal186;
    $scope.patternAltura = $scope.patternLongitud = $scope.patternProfundidad = PROPERTIES.expresionesRegulares.decimal102;
    $scope.patternPeso = PROPERTIES.expresionesRegulares.decimal166;
    $scope.patternFactorHoraHombre = PROPERTIES.expresionesRegulares.decimal62;

    if($scope.action !== -1){
        $scope.color = '#F6CF99';
        $scope.cliente = data.cliente;
        console.log($scope.cliente);
        if($scope.cliente.tipo_identificacion !== '' && $scope.cliente.tipo_identificacion !== null){
            for(var i in $scope.catalogos.tiposIdentificacion){
                if($scope.catalogos.tiposIdentificacion[i].codigo == $scope.cliente.tipo_identificacion){
                    $scope.tipoIdentificacionSelected = $scope.catalogos.tiposIdentificacion[i];
                }
            }
        } else {
            $scope.tipoIdentificacionSelected = {};
        }
        if($scope.cliente.condicion_pago !== '' && $scope.cliente.condicion_pago !== null){
            for(var i in $scope.catalogos.condicionesPago){
                if($scope.catalogos.condicionesPago[i].codigo == $scope.cliente.condicion_pago){
                    $scope.condicionPagoSelected = $scope.catalogos.condicionesPago[i];
                }
            }
        } else {
            $scope.condicionPagoSelected = {};
        }
    } else {
        $scope.color = '#B7EDB7';
        $scope.cliente = {
            codigo : '', identificacion: '', razon_social : '', direccion : '', telefono : '', email : '', parte_relacionada : false
        };
        $scope.tipoIdentificacionSelected = {};
        $scope.condicionPagoSelected = {};

    }

    $scope.cancel = function(){
        $modalInstance.dismiss('Canceled');
    };

    $scope.submitForm = function(){
        var dataRequest = {
            tabla : 'personas',
            campos : {codigo : $scope.cliente.codigo, razon_social : $scope.cliente.razon_social, identificacion : $scope.cliente.identificacion},
            labels : {codigo : 'Código', razon_social : 'Razón Social', identificacion : 'Identificación'}
        };
        if($scope.clienteForm.$valid){
            if($scope.action === -1){
                clientesService.existeCampos(dataRequest, $scope.user.token)
                    .then(function(result){
                        angular.element('#div-loading').hide();
                        if(result.status == "OK"){
                            $scope.setCliente();
                        } else {
                            dialogs.error('Error', result.message);
                        }
                    }).catch(function(data){
                        angular.element('#div-loading').hide();
                        if(data.status == "OFF"){
                            $modalInstance.close(data);
                        } else {
                            dialogs.error('Error', data.message);
                        }
                    });
            } else {
                $scope.setCliente();
            }
        }
    };

    $scope.setCliente = function(){
        if(Object.keys($scope.tipoIdentificacionSelected).length !== 0){
            $scope.cliente.tipo_identificacion = $scope.tipoIdentificacionSelected.codigo;
        }
        if(Object.keys($scope.condicionPagoSelected).length !== 0){
            $scope.cliente.condicion_pago = $scope.condicionPagoSelected.codigo;
        }
        if($scope.cliente.parte_relacionada){
            $scope.cliente.parte_relacionada = 't';
        } else {
            $scope.cliente.parte_relacionada = 'f';
        }
        var dataRequest = {
            action : $scope.action,
            cliente : $scope.cliente,
            userId : $scope.user.userId
        };

        angular.element('#div-loading').show();
        clientesService.setCliente(dataRequest, $scope.user.token)
            .then(function(result){
                angular.element('#div-loading').hide();
                if(result.status == "OK"){
                    dialogs.notify(undefined, result.message);
                    $modalInstance.close(result);
                } else {
                    dialogs.error('Error', result.message);
                }
            }).catch(function(data){
                angular.element('#div-loading').hide();
                if(data.status == "OFF"){
                    $modalInstance.close(data);
                } else {
                    dialogs.error('Error', data.message);
                }
            });

    };


});

myInventario.controller("buscarClienteDialogCtrl",function($scope,$modalInstance,data){
    angular.element('#div-loading').hide();
    $scope.searchCriteria = {
        codigo : data.searchCriteria.codigo,
        identificacion : data.searchCriteria.identificacion,
        razon_social : data.searchCriteria.razon_social,
        email : data.searchCriteria.email
    };

    $scope.cancel = function(){
        $modalInstance.dismiss('Canceled');
    };

    $scope.submitForm = function(){
        $modalInstance.close($scope.searchCriteria);
    };
});
