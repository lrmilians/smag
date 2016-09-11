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

myAdmin.controller("diarioCtrl", ['catalogsContabilidadService','diarioService','$scope','$modal','dialogs','$location','$log','$cookieStore', '$translate',
    function(catalogsContabilidadService,diarioService,$scope,$modal,dialogs,$location,$log,$cookieStore,$translate) {

        var diarioCtrl = this;
        diarioCtrl.currentPage = 1;
        diarioCtrl.pageSize = 100;
        diarioCtrl.totalRecords = 0;
        diarioCtrl.advanceSearch = false;

       // diarioCtrl.q = '';

        diarioCtrl.dataRequest = {
            fecha : "",
            descripcion : "",
            importe_debe : "",
            importe_haber : "",
            cuenta_debe_id : "",
            cuenta_haber_id : "",
            user_id : "",
            created : "",
            modified : "",
            start : 0,
            size : diarioCtrl.pageSize
        };

        diarioCtrl.orderByField = 'id';
        diarioCtrl.reverseSort = false;

        if($cookieStore.get('user') == undefined){
            $location.path("/");
        } else {
            diarioCtrl.user = $cookieStore.get('user');
        }
        if(diarioCtrl.user.rolCode == 'CL01'){
            diarioCtrl.dataRequest.user_id = diarioCtrl.user.userId;
        }

        diarioCtrl.initCtrl = function(){
            angular.element('#div-loading').show();
            var dataRequest = {start : '', size : ''};
            catalogsContabilidadService.getCuentas(diarioCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == 'OK'){
                        diarioCtrl.cuentas = result.data;
                        var nombre = '';
                        for(var i in diarioCtrl.cuentas){
                            if(diarioCtrl.cuentas[i].nombre.length > 40){
                                nombre = diarioCtrl.cuentas[i].nombre.slice(0,40) + '...';
                            } else {
                                nombre = diarioCtrl.cuentas[i].nombre;
                            }
                            diarioCtrl.cuentas[i].codNombre = diarioCtrl.cuentas[i].codigo + ' - ' + nombre;
                        }
                        var indexValue = (diarioCtrl.currentPage - 1) * diarioCtrl.pageSize;
                        diarioCtrl.dataRequest.start = indexValue;
                        diarioCtrl.getAsientos(diarioCtrl.dataRequest);
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

        diarioCtrl.pageChangeHandler = function(newPageNumber){
            diarioCtrl.currentPage = newPageNumber;
            var indexValue = (diarioCtrl.currentPage - 1) * diarioCtrl.pageSize;
            diarioCtrl.dataRequest.start =  indexValue;
            diarioCtrl.getAsientos(diarioCtrl.dataRequest);
        }

        diarioCtrl.resetSearch = function(){
            diarioCtrl.dataRequest = {
                fecha : "",
                descripcion : "",
                importe_debe : "",
                importe_haber : "",
                cuenta_debe_id : "",
                cuenta_haber_id : "",
                user_id : "",
                created : "",
                modified : "",
                start : 0,
                size : diarioCtrl.pageSize
            }
            if(diarioCtrl.user.rolCode == 'CL01'){
                diarioCtrl.dataRequest.user_id = diarioCtrl.user.userId;
            }
            diarioCtrl.advanceSearch = false;
            diarioCtrl.getAsientos(diarioCtrl.dataRequest);
        }

        diarioCtrl.searchAsiento = function(){
            angular.element('#div-loading').show();
            var dlg = dialogs.create('modules/contabilidad/views/dialog-form/form-search-asiento.html','searchAsientoDialogCtrl',{
                cuentas : diarioCtrl.cuentas, searchCriteria : diarioCtrl.dataRequest
            },'lg');
            dlg.result.then(function(result){
                diarioCtrl.currentPage = 1;
                var indexValue = (diarioCtrl.currentPage - 1) * diarioCtrl.pageSize;
                diarioCtrl.dataRequest.pid = result.pid;
                if(result.perfil != ""){
                    diarioCtrl.dataRequest.perfil = result.perfil.id;
                } else {
                    diarioCtrl.dataRequest.perfil = result.perfil;
                }
                diarioCtrl.dataRequest.nombre = result.nombre;
                diarioCtrl.dataRequest.apellido = result.apellido;
                if(result.paisNacimiento != ""){
                    diarioCtrl.dataRequest.paisNacimiento = result.paisNacimiento.id;
                } else {
                    diarioCtrl.dataRequest.paisNacimiento = result.paisNacimiento;
                }
                if(result.paisResidencia != ""){
                    diarioCtrl.dataRequest.paisResidencia = result.paisResidencia.id;
                } else {
                    diarioCtrl.dataRequest.paisResidencia = result.paisResidencia;
                }
                diarioCtrl.dataRequest.inicio = indexValue;

                if(diarioCtrl.dataRequest.pid != "" || diarioCtrl.dataRequest.perfil != "" || diarioCtrl.dataRequest.nombre != "" ||
                    diarioCtrl.dataRequest.apellido != "" || diarioCtrl.dataRequest.paisNacimiento != "" || diarioCtrl.dataRequest.paisResidencia != "" ||
                    diarioCtrl.dataRequest.proceso != ""){
                    diarioCtrl.advanceSearch = true;
                }
                diarioCtrl.getAsientos(diarioCtrl.dataRequest);

            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        }

        diarioCtrl.getAsientos = function(dataRequest){
            angular.element('#div-loading').show();
            diarioService.getAsientos(dataRequest, diarioCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == "OK"){
                        diarioCtrl.asientos = result.data;
                        diarioCtrl.totalRecords = result.total_rows;
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

        diarioCtrl.addAsiento = function(){
            var dlg = dialogs.create('client/app/modules/contabilidad/views/dialog-form/form-asiento.html','asientoDialogCtrl',{
                userToken : diarioCtrl.user.token,
                cuentas : diarioCtrl.cuentas,
                asientoId : -1,
                asiento : {}
            },'lg');
            dlg.result.then(function(result){
                if(result.status == "OK"){
                    diarioCtrl.initCtrl();
                }
            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        }

        diarioCtrl.editAsiento = function(asiento){
            var dlg = dialogs.create('client/app/modules/contabilidad/views/dialog-form/form-asiento.html','asientoDialogCtrl',{
                userToken : diarioCtrl.user.token,
                cuentas : diarioCtrl.cuentas,
                asientoId : asiento.id,
                asiento : asiento
            },'lg');
            dlg.result.then(function(result){
                if(result.status == "OK"){
                    diarioCtrl.initCtrl();
                }
            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        }

        diarioCtrl.delAsiento = function(id){
            dialogs.confirm(undefined, '¿Desea eliminar le asiento contable?').result.then(function(btn){
                var dataRequest = {
                    id : id
                };
                angular.element('#div-loading').show();
                diarioService.delAsiento(dataRequest, diarioCtrl.user.token)
                    .then(function(result){
                        angular.element('#div-loading').hide();
                        if(result.status == "OK"){
                            dialogs.notify(undefined, 'Asiento eliminado correctamente.');
                            diarioCtrl.initCtrl();
                        } else {
                            dialogs.error('Error', result.data);
                        }
                    }).catch(function(data){
                        angular.element('#div-loading').hide();
                        dialogs.error('Error', data.message + ' ' + data.data);
                    });
            },function(btn){
            });
        }

        diarioCtrl.cierreContable = function(){
            var dlg = dialogs.create('client/app/modules/contabilidad/views/dialog-form/form-cierre-contable.html','cierreContableDialogCtrl',{
                userToken : diarioCtrl.user.token
            },'lg');
            dlg.result.then(function(result){
                if(result.status == "OK"){
                    diarioCtrl.initCtrl();
                }
            },function(){
                if(angular.equals($scope.name,''))
                    $scope.name = 'You did not enter in your name!';
            });
        }

}]);


myAdmin.controller("asientoDialogCtrl", function(PROPERTIES_CONTABILIDAD,diarioService,$scope,$modalInstance,data,$translate,dialogs){

    $scope.userToken = data.userToken;
    $scope.cuentas = data.cuentas;
    $scope.asientoId = data.asientoId;
    $scope.file = {};

    $scope.patternImporte = PROPERTIES_CONTABILIDAD.regularExpression.number;

    $scope.tipoCuentas =  PROPERTIES_CONTABILIDAD.tipoCuentas;
    $scope.tipoCuentaSelected = {};
    $scope.cuentasDebe =  [];
    $scope.cuentasHaber =  [];
    $scope.msg = '';
    $scope.totalImporteDebe = 0;
    $scope.totalImporteHaber = 0;
    $scope.totalDif = 0;
    $scope.style = 'danger text-danger';

    $scope.glyphicon = "glyphicon-lock";

    $scope.cancel = function(){
        $modalInstance.dismiss('Canceled');
    };


    if(Object.keys(data.asiento).length !== 0){
        $scope.asiento = {
            fecha : data.asiento.fecha.substr(0,10),
            descripcion : data.asiento.descripcion,
            importe : data.asiento.importe_debe
        };
        for(var i in $scope.cuentas){
            if($scope.cuentas[i].codigo == data.asiento.cuenta_debe){
                $scope.asiento.cuentaDebeSelected = $scope.cuentas[i];
            }
        }
        for(var i in $scope.cuentas){
            if($scope.cuentas[i].codigo == data.asiento.cuenta_haber){
                $scope.asiento.cuentaHaberSelected = $scope.cuentas[i];
            }
        }
        $scope.dataEmpty = false;
    } else {
        $scope.asiento = {
            fecha : '',
            descripcion : '',
            importe : ''
        };
        $scope.dataEmpty = true;
    }


    $scope.addCuenta = function(){
        if ($scope.asientoForm.$valid && $scope.asiento.importe !== ''){
            switch ($scope.asiento.tipoCuentaSelected.id){
                case 0:
                    $scope.cuentasDebe.push({
                        id : $scope.asiento.cuentaSelected.id,
                        codigo : $scope.asiento.cuentaSelected.codigo,
                        importe : $scope.asiento.importe
                    });
                    $scope.totalImporteDebe = 0;
                    for(var i in $scope.cuentasDebe){
                        $scope.totalImporteDebe = $scope.totalImporteDebe + parseFloat($scope.cuentasDebe[i].importe);
                    }
                    break;
                case 1:
                    $scope.cuentasHaber.push({
                        id : $scope.asiento.cuentaSelected.id,
                        codigo : $scope.asiento.cuentaSelected.codigo,
                        importe : $scope.asiento.importe
                    });
                    $scope.totalImporteHaber = 0;
                    for(var i in $scope.cuentasHaber){
                        $scope.totalImporteHaber = $scope.totalImporteHaber + parseFloat($scope.cuentasHaber[i].importe);
                    }
                    break;
            }

            if($scope.totalImporteDebe.toFixed(2) == $scope.totalImporteHaber.toFixed(2)){
                $scope.style = 'success text-success';
                $scope.totalDif = 0;
            } else {
                $scope.style = 'danger text-danger';
                $scope.totalDif = $scope.totalImporteDebe.toFixed(2) - $scope.totalImporteHaber.toFixed(2);
            }
            $scope.asiento.cuentaSelected = {};
            $scope.asiento.tipoCuentaSelected = {};
            $scope.asiento.importe = '';
            $scope.msg = '';
        } else {
            $scope.msg = 'Existen campos sin valores.'
        }
    }

    $scope.delCuenta = function (tipoCuenta, id) {
        switch (tipoCuenta){
            case 0:
                for(var i in $scope.cuentasDebe){
                    if($scope.cuentasDebe[i].id === id){
                        $scope.cuentasDebe.splice(i,1);
                    }
                }
                $scope.totalImporteDebe = 0;
                for(var i in $scope.cuentasDebe){
                    $scope.totalImporteDebe = $scope.totalImporteDebe + parseFloat($scope.cuentasDebe[i].importe);
                }
                break;
            case 1:
                for(var i in $scope.cuentasHaber){
                    if($scope.cuentasHaber[i].id === id){
                        $scope.cuentasHaber.splice(i,1);
                    }
                }
                $scope.totalImporteHaber = 0;
                for(var i in $scope.cuentasHaber){
                    $scope.totalImporteHaber = $scope.totalImporteHaber + parseFloat($scope.cuentasHaber[i].importe);
                }
                break;
        }

        if($scope.totalImporteDebe.toFixed(2) == $scope.totalImporteHaber.toFixed(2)){
            $scope.style = 'success text-success';
            $scope.totalDif = 0;
        } else {
            $scope.style = 'danger text-danger';
            $scope.totalDif = $scope.totalImporteDebe.toFixed(2) - $scope.totalImporteHaber.toFixed(2);
        }
    }

    $scope.submitForm = function(){
        if ($scope.asientoForm.$valid){
            if($scope.totalImporteDebe.toFixed(2) === $scope.totalImporteHaber.toFixed(2) && $scope.totalImporteDebe.toFixed(2) !== 0 && $scope.totalImporteHaber.toFixed(2) !== 0){
                dialogs.confirm(undefined, '¿Está seguro que desea realizar la contabilización?').result.then(function(btn){
                    var dataRequest = {
                        action : $scope.asientoId,
                        fecha : $scope.asiento.fecha,
                        descripcion : $scope.asiento.descripcion,
                        importe : $scope.totalImporteDebe,
                        cuentas_debe : $scope.cuentasDebe,
                        cuentas_haber : $scope.cuentasHaber
                    };
                    angular.element('#div-loading').show();
                    diarioService.addAsiento(dataRequest, $scope.userToken)
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
                },function(btn){
                });
            } else {
                if($scope.totalImporteDebe.toFixed(2) !== $scope.totalImporteHaber.toFixed(2)){
                    dialogs.error('Error', 'El total de DEBE es distinto al HABER');
                } else {
                    dialogs.error('Error', 'No existen cuentas para realizar la operación');
                }

            }
        } else {
            dialogs.error('Error', 'Existen campos obligatorios por llenar');
        }
    };
});


myAdmin.controller("cierreContableDialogCtrl", function(PROPERTIES_CONTABILIDAD,diarioService,$scope,$modalInstance,data,$translate,dialogs){

    $scope.userToken = data.userToken;
    $scope.fecha = '';

    $scope.cancel = function(){
        $modalInstance.dismiss('Canceled');
    };

    $scope.submitForm = function(){
        if ($scope.asientoForm.$valid){
            dialogs.confirm(undefined, '¿Está seguro que desea realizar el cierrre contable?').result.then(function(btn){
                var dataRequest = {
                    fecha : $scope.fecha,
                };
                angular.element('#div-loading').show();
                diarioService.cierreContable(dataRequest, $scope.userToken)
                    .then(function(result){
                        angular.element('#div-loading').hide();
                        if(result.status == "OK"){
                            dialogs.notify(undefined, 'Cierre contable realizado con éxito.');
                            $modalInstance.close(result);
                        } else {
                            dialogs.error('Error', result.data);
                        }
                    }).catch(function(data){
                        angular.element('#div-loading').hide();
                        dialogs.error('Error', data.message + ' ' + data.data);
                    });
            },function(btn){
            });
        } else {
            dialogs.error('Error', 'Existen campos obligatorios por llenar');
        }
    };
});
