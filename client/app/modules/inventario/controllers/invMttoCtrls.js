'use strict';
/**
 * @ngdoc function
 * @name prometeoApp.controller:invMttoCtrl,
 *
 * @description
 * # invMttoCtrl,
 * Controller of the esignatureApp
 */

myInventario.controller("invMttoCtrl", ['PROPERTIES','invMttoService','$scope','$modal','dialogs','$location','$log','$cookieStore','$translate',
    function(PROPERTIES,invMttoService,$scope,$modal,dialogs,$location,$log,$cookieStore,$translate) {

        var invMttoCtrl = this;

        invMttoCtrl.currentPage = 1;
        invMttoCtrl.pageSize = 50;
        invMttoCtrl.totalRecords = 0;
        invMttoCtrl.advanceSearch = false;

        invMttoCtrl.dataRequest = {
            codigo: '',
            nombre: '',
            codigo_barras: '',
            start: 0,
            size: invMttoCtrl.pageSize,
            catalogos : [
                PROPERTIES.maetroTablas.categoriasProducto,
                PROPERTIES.maetroTablas.tiposProducto,
                PROPERTIES.maetroTablas.marcasProducto,
                PROPERTIES.maetroTablas.modelosProducto,
                PROPERTIES.maetroTablas.unidadesMedida,
                PROPERTIES.maetroTablas.ivas,
                PROPERTIES.maetroTablas.estadosProducto,
                PROPERTIES.maetroTablas.icesCompra,
                PROPERTIES.maetroTablas.icesVenta
            ]
        };

        invMttoCtrl.orderByField = 'numero';
        invMttoCtrl.reverseSort = false;

        if($cookieStore.get('user') == undefined){
            $scope.$parent.logout(true);
            $location.path("/");
        } else {
            invMttoCtrl.user = $cookieStore.get('user');
        }

        invMttoCtrl.initCtrl = function(){
            if(invMttoCtrl.user !== undefined){
                invMttoCtrl.getProductos();
            }
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
                codigo_barras: '',
                start: 0,
                size: invMttoCtrl.pageSize,
                catalogos : [
                    PROPERTIES.maetroTablas.categoriasProducto,
                    PROPERTIES.maetroTablas.tiposProducto,
                    PROPERTIES.maetroTablas.marcasProducto,
                    PROPERTIES.maetroTablas.modelosProducto,
                    PROPERTIES.maetroTablas.unidadesMedida,
                    PROPERTIES.maetroTablas.ivas,
                    PROPERTIES.maetroTablas.estadosProducto,
                    PROPERTIES.maetroTablas.icesCompra,
                    PROPERTIES.maetroTablas.icesVenta
                ]
            };

            invMttoCtrl.advanceSearch = false;
            invMttoCtrl.getProductos();
        };

        invMttoCtrl.searchProducto = function(){
            angular.element('#div-loading').show();
            var dlg = dialogs.create('client/app/modules/inventario/views/dialog-form/form-buscar-producto.html','buscarProductoDialogCtrl',{
                searchCriteria : invMttoCtrl.dataRequest
            },'lg');
            dlg.result.then(function(result){
                invMttoCtrl.currentPage = 1;
                var indexValue = (invMttoCtrl.currentPage - 1) * invMttoCtrl.pageSize;
                invMttoCtrl.dataRequest.codigo = result.codigo;
                invMttoCtrl.dataRequest.nombre = result.nombre;
                invMttoCtrl.dataRequest.codigo_barras = result.codigo_barras;
                invMttoCtrl.dataRequest.inicio = indexValue;

                if(invMttoCtrl.dataRequest.codigo != "" || invMttoCtrl.dataRequest.nombre != "" || invMttoCtrl.dataRequest.codigo_barras != ""){
                    invMttoCtrl.advanceSearch = true;
                }
                invMttoCtrl.getProductos();

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
                        invMttoCtrl.catalogos = {
                            categoriasProducto : '', tiposProducto : '', marcasProducto : '', modelosProducto : '', unidadesMedida : '',
                            ivas : '', estadosProducto : '', icesCompra : '', icesVenta : ''
                        };
                        for(var i in result.catalogos){
                            for(var j in invMttoCtrl.dataRequest.catalogos){
                                if(i == invMttoCtrl.dataRequest.catalogos[j]){
                                    switch(j){
                                        case '0':
                                            invMttoCtrl.catalogos.categoriasProducto = result.catalogos[i];
                                            break;
                                        case '1':
                                            invMttoCtrl.catalogos.tiposProducto = result.catalogos[i];
                                            break;
                                        case '2':
                                            invMttoCtrl.catalogos.marcasProducto = result.catalogos[i];
                                            break;
                                        case '3':
                                            invMttoCtrl.catalogos.modelosProducto = result.catalogos[i];
                                            break;
                                        case '4':
                                            invMttoCtrl.catalogos.unidadesMedida = result.catalogos[i];
                                            break;
                                        case '5':
                                            invMttoCtrl.catalogos.ivas = result.catalogos[i];
                                            break;
                                        case '6':
                                            invMttoCtrl.catalogos.estadosProducto = result.catalogos[i];
                                            break;
                                        case '7':
                                            invMttoCtrl.catalogos.icesCompra = result.catalogos[i];
                                            break;
                                        case '8':
                                            invMttoCtrl.catalogos.icesVenta = result.catalogos[i];
                                            break;
                                    }
                                }
                            }

                        }
                        for(var i in invMttoCtrl.productos){
                            for(var j in invMttoCtrl.catalogos.estadosProducto){
                                if(invMttoCtrl.productos[i].estado == invMttoCtrl.catalogos.estadosProducto[j].codigo){
                                    invMttoCtrl.productos[i].estadoNombre = invMttoCtrl.catalogos.estadosProducto[j].nombre;
                                }
                            }
                            for(var j in invMttoCtrl.catalogos.ivas){
                                if(invMttoCtrl.productos[i].iva == invMttoCtrl.catalogos.ivas[j].codigo){
                                    invMttoCtrl.productos[i].ivaNombre = invMttoCtrl.catalogos.ivas[j].nombre;
                                }
                            }
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

        invMttoCtrl.addProducto = function(){
            var dlg = dialogs.create('client/app/modules/inventario/views/dialog-form/form-producto.html','productoDialogCtrl',{
                action : -1, user : invMttoCtrl.user, catalogos : invMttoCtrl.catalogos
            },{size : 'md'});
            dlg.result.then(function(result){
                if(result.status == "OK"){
                    invMttoCtrl.initCtrl();
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

        invMttoCtrl.editProducto = function(producto){
            var dlg = dialogs.create('client/app/modules/inventario/views/dialog-form/form-producto.html','productoDialogCtrl',{
                action : producto.id, user: invMttoCtrl.user, catalogos : invMttoCtrl.catalogos, producto : producto
            },{size : 'md'});
            dlg.result.then(function(result){
                if(result.status == "OK"){
                    invMttoCtrl.initCtrl();
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

myInventario.controller("productoDialogCtrl", function(PROPERTIES,invMttoService,$scope,$modalInstance,data,$translate,dialogs){
    $scope.catalogos = data.catalogos;
    $scope.action = data.action;
    $scope.user = data.user;

    $scope.glyphicon = "glyphicon-lock";
    $scope.tablas = [];

    $scope.patternCodigo = PROPERTIES.expresionesRegulares.codigoProducto;
    $scope.patternCodigoBarras = PROPERTIES.expresionesRegulares.codigoBarrasProducto;
    $scope.patternNombre = PROPERTIES.expresionesRegulares.nombreProducto;
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
        $scope.producto = data.producto;
        if($scope.producto.categoria !== '' && $scope.producto.categoria !== null){
            for(var i in $scope.catalogos.categoriasProducto){
                if($scope.catalogos.categoriasProducto[i].codigo == $scope.producto.categoria){
                    $scope.categoriaProductoSelected = $scope.catalogos.categoriasProducto[i];
                }
            }
        } else {
            $scope.categoriaProductoSelected = {};
        }
        if($scope.producto.tipo_producto !== '' && $scope.producto.tipo_producto !== null){
            for(var i in $scope.catalogos.tiposProducto){
                if($scope.catalogos.tiposProducto[i].codigo == $scope.producto.tipo_producto){
                    $scope.tipoProductoSelected = $scope.catalogos.tiposProducto[i];
                }
            }
        } else {
            $scope.tipoProductoSelected = {};
        }
        if($scope.producto.marca !== '' && $scope.producto.marca !== null){
            for(var i in $scope.catalogos.marcasProducto){
                if($scope.catalogos.marcasProducto[i].codigo == $scope.producto.marca){
                    $scope.marcaProductoSelected = $scope.catalogos.marcasProducto[i];
                }
            }
        } else {
            $scope.marcaProductoSelected = {};
        }
        if($scope.producto.modelo !== '' && $scope.producto.modelo !== null){
            for(var i in $scope.catalogos.modelosProducto){
                if($scope.catalogos.modelosProducto[i].codigo == $scope.producto.modelo){
                    $scope.modeloProductoSelected = $scope.catalogos.modelosProducto[i];
                }
            }
        } else {
            $scope.modeloProductoSelected = {};
        }
        if($scope.producto.unidad_medida !== '' && $scope.producto.unidad_medida !== null){
            for(var i in $scope.catalogos.unidadesMedida){
                if($scope.catalogos.unidadesMedida[i].codigo == $scope.producto.unidad_medida){
                    $scope.unidadMedidaSelected = $scope.catalogos.unidadesMedida[i];
                }
            }
        } else {
            $scope.unidadMedidaSelected = {};
        }
        if($scope.producto.iva !== '' && $scope.producto.iva !== null){
            for(var i in $scope.catalogos.ivas){
                if($scope.catalogos.ivas[i].codigo == $scope.producto.iva){
                    $scope.ivaSelected = $scope.catalogos.ivas[i];
                }
            }
        } else {
            $scope.ivaSelected = {};
        }
        if($scope.producto.estado !== '' && $scope.producto.estado !== null){
            for(var i in $scope.catalogos.estadosProducto){
                if($scope.catalogos.estadosProducto[i].codigo == $scope.producto.estado){
                    $scope.estadoSelected = $scope.catalogos.estadosProducto[i];
                }
            }
        } else {
            $scope.estadoSelected = {};
        }
        if($scope.producto.ice_compras !== '' && $scope.producto.ice_compras !== null){
            for(var i in $scope.catalogos.icesCompra){
                if($scope.catalogos.icesCompra[i].codigo == $scope.producto.ice_compras){
                    $scope.iceCompraSelected = $scope.catalogos.icesCompra[i];
                }
            }
        } else {
            $scope.iceCompraSelected = {};
        }
        if($scope.producto.ice_ventas !== '' && $scope.producto.ice_ventas !== null){
            for(var i in $scope.catalogos.icesVenta){
                if($scope.catalogos.icesVenta[i].codigo == $scope.producto.ice_ventas){
                    $scope.iceVentaSelected = $scope.catalogos.icesVenta[i];
                }
            }
        } else {
            $scope.iceVentaSelected = {};
        }
    } else {
        $scope.color = '#B7EDB7';
        $scope.producto = {
            nombre : '', codigo : '', codigo_barras: '', categoria : '', tipo_producto : '', marca : '', modelo : '', unidad_medida : '',
            precio_venta: '', iva : '', estado : '', referencia : '', descripcion : '', stock_actual : '', stock_minimo : '', stock_maximo : '', ubicacion : '',
            costo_ultima_compra : '', costo_primera_compra : '', ice_compras : '', ice_ventas : '', peso : '', factor_hora_hombre : '', altura : '',
            longitud : '', profundidad : ''
        };
        $scope.categoriaProductoSelected = {};
        $scope.tipoProductoSelected = {};
        $scope.marcaProductoSelected = {};
        $scope.modeloProductoSelected = {};
        $scope.unidadMedidaSelected = {};
        $scope.ivaSelected = {};
        $scope.estadoSelected = {};
        $scope.iceCompraSelected = {};
        $scope.iceVentaSelected = {};

    }

    $scope.cancel = function(){
        $modalInstance.dismiss('Canceled');
    };

    $scope.submitForm = function(){
        var dataRequest = {
            tabla : 'inv_productos',
            campos : {codigo : $scope.producto.codigo,nombre : $scope.producto.nombre, codigo_barras : $scope.producto.codigo_barras},
            labels : {codigo : 'Código', nombre : 'Nombre', codigo_barras : 'Código Barras'}
        };
        if ($scope.productoForm.$valid){
            if($scope.action === -1){
                invMttoService.existeCampos(dataRequest, $scope.user.token)
                    .then(function(result){
                        angular.element('#div-loading').hide();
                        if(result.status == "OK"){
                            $scope.setProducto();
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
                $scope.setProducto();
            }
        }
    };

    $scope.setProducto = function(){
        if(parseFloat($scope.producto.stock_minimo) <= parseFloat($scope.producto.stock_maximo)){
            if(parseFloat($scope.producto.stock_actual) >= parseFloat($scope.producto.stock_minimo) && parseFloat($scope.producto.stock_actual) <= parseFloat($scope.producto.stock_maximo)){
                if(Object.keys($scope.categoriaProductoSelected).length !== 0){
                    $scope.producto.categoria = $scope.categoriaProductoSelected.codigo;
                }
                if(Object.keys($scope.tipoProductoSelected).length !== 0){
                    $scope.producto.tipo_producto = $scope.tipoProductoSelected.codigo;
                }
                if(Object.keys($scope.marcaProductoSelected).length !== 0){
                    $scope.producto.marca = $scope.marcaProductoSelected.codigo;
                }
                if(Object.keys($scope.modeloProductoSelected).length !== 0){
                    $scope.producto.modelo = $scope.modeloProductoSelected.codigo;
                }
                if(Object.keys($scope.unidadMedidaSelected).length !== 0){
                    $scope.producto.unidad_medida = $scope.unidadMedidaSelected.codigo;
                }
                if(Object.keys($scope.ivaSelected).length !== 0){
                    $scope.producto.iva = $scope.ivaSelected.codigo;
                }
                if(Object.keys($scope.estadoSelected).length !== 0){
                    $scope.producto.estado = $scope.estadoSelected.codigo;
                }
                if(Object.keys($scope.iceCompraSelected).length !== 0){
                    $scope.producto.ice_compras = $scope.iceCompraSelected.codigo;
                }
                if(Object.keys($scope.iceVentaSelected).length !== 0){
                    $scope.producto.ice_ventas = $scope.iceVentaSelected.codigo;
                }
                var dataRequest = {
                    action : $scope.action,
                    producto : $scope.producto,
                    userId : $scope.user.userId
                };
                angular.element('#div-loading').show();
                invMttoService.setProducto(dataRequest, $scope.user.token)
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
            } else {
                dialogs.error('Error', 'El stock actual (' + $scope.producto.stock_actual + ') no se encuentra entre el mínimo y máximo (' + $scope.producto.stock_minimo + ' y ' + $scope.producto.stock_maximo + ').');
            }
        } else {
            dialogs.error('Error', 'El stock mínimo (' + $scope.producto.stock_minimo + ') es mayor que el máximo (' + $scope.producto.stock_maximo + ').');
        }
    };


});

myInventario.controller("buscarProductoDialogCtrl",function($scope,$modalInstance,data){
    angular.element('#div-loading').hide();
    $scope.searchCriteria = {
        codigo : data.searchCriteria.codigo,
        nombre : data.searchCriteria.nombre,
        codigo_barras : data.searchCriteria.codigo_barras
    };

    $scope.cancel = function(){
        $modalInstance.dismiss('Canceled');
    };

    $scope.submitForm = function(){
        $modalInstance.close($scope.searchCriteria);
    };
});
