'use strict';
/**
 * @ngdoc function
 * @name prometeoApp.controller:invMttoCtrl,
 *
 * @description
 * # invMttoCtrl,
 * Controller of the esignatureApp
 */

myInventario.controller("invMttoCtrl", ['invMttoService','$scope','$modal','dialogs','$location','$log','$cookieStore',
    function(invMttoService,$scope,$modal,dialogs,$location,$log,$cookieStore) {

        var invMttoCtrl = this;

        invMttoCtrl.totalSumaDebe = 0;
        invMttoCtrl.totalSumaHaber = 0;
        invMttoCtrl.totalSaldoDeudor = 0;
        invMttoCtrl.totalSaldoAcreedor = 0;
        invMttoCtrl.estiloSuma = '';
        invMttoCtrl.estiloSaldos = '';

        if($cookieStore.get('user') == undefined){
            $location.path("/");
        } else {
            invMttoCtrl.user = $cookieStore.get('user');
        }
        if(invMttoCtrl.user.rolCode == 'CL01'){
            invMttoCtrl.dataRequest.user_id = invMttoCtrl.user.userId;
        }

        invMttoCtrl.initCtrl = function(){
            /*  invMttoCtrl.detalleCuenta = false;
            invMttoCtrl.detallesCuenta = {};
            angular.element('#div-loading').show();
            var dataRequest = {start : '', size : ''};
           invMttoService.getBalanceComprobacion(invMttoCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == 'OK'){
                        invMttoCtrl.balance = result.data.data;
                        var total_suma_debe = 0;
                        var total_suma_haber = 0;
                        var total_saldo_deudor = 0;
                        var total_saldo_acreedor = 0;

                        for(var i in invMttoCtrl.balance){
                            total_suma_debe = total_suma_debe + parseFloat(invMttoCtrl.balance[i].suma_debe);
                            total_suma_haber = total_suma_haber + parseFloat(invMttoCtrl.balance[i].suma_haber);
                            switch (true){
                                case parseFloat(invMttoCtrl.balance[i].saldo) > 0:
                                    invMttoCtrl.balance[i].saldo_deudor = parseFloat(invMttoCtrl.balance[i].saldo);
                                    invMttoCtrl.balance[i].saldo_acreedor = 0;
                                    break;
                                case invMttoCtrl.balance[i].saldo < 0:
                                    invMttoCtrl.balance[i].saldo_deudor = 0;
                                    invMttoCtrl.balance[i].saldo_acreedor = parseFloat(invMttoCtrl.balance[i].saldo);
                                    break;
                                default:
                                    invMttoCtrl.balance[i].saldo_deudor = 0;
                                    invMttoCtrl.balance[i].saldo_acreedor = 0;
                                    break;
                            }
                            total_saldo_deudor = total_saldo_deudor + parseFloat(invMttoCtrl.balance[i].saldo_deudor);
                            total_saldo_acreedor = total_saldo_acreedor + (parseFloat(invMttoCtrl.balance[i].saldo_acreedor) * -1);
                        }
                        invMttoCtrl.totalSumaDebe = total_suma_debe.toFixed(2);
                        invMttoCtrl.totalSumaHaber = total_suma_haber.toFixed(2);
                        invMttoCtrl.totalSaldoDeudor = total_saldo_deudor.toFixed(2);
                        invMttoCtrl.totalSaldoAcreedor = total_saldo_acreedor.toFixed(2);
                        switch (true){
                            case invMttoCtrl.totalSumaDebe === invMttoCtrl.totalSumaDebe:
                                invMttoCtrl.estiloSuma = 'success text-success';
                                break;
                            case invMttoCtrl.totalSumaDebe !== invMttoCtrl.totalSumaDebe:
                                invMttoCtrl.estiloSuma = 'danger text-danger';
                                break;
                        }
                        switch (true){
                            case invMttoCtrl.totalSaldoDeudor === invMttoCtrl.totalSaldoAcreedor:
                                invMttoCtrl.estiloSaldos = 'success text-success';
                                break;
                            case invMttoCtrl.totalSaldoDeudor !== invMttoCtrl.totalSaldoAcreedor:
                                invMttoCtrl.estiloSaldos = 'danger text-danger';
                                break;
                        }
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
                });*/

        }

        invMttoCtrl.verDetalleCuenta = function(cuentaId){
            angular.element('#div-loading').show();
            mayorService.getDetalleCuenta(cuentaId, invMttoCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    invMttoCtrl.detalleCuenta = true;
                    if(result.status == 'OK'){
                        invMttoCtrl.detallesCuenta = result.data;
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

}]);

/*myAdmin.controller("balanceSituacionCtrl", ['catalogsContabilidadService','balanceSituacionService','$scope','$modal','dialogs','$location','$log','$cookieStore', '$translate',
    function(catalogsContabilidadService,balanceSituacionService,$scope,$modal,dialogs,$location,$log,$cookieStore,$translate) {

        var balanceSituacionCtrl = this;

        balanceSituacionCtrl.totalSumaDebe = 0;
        balanceSituacionCtrl.totalSumaHaber = 0;
        balanceSituacionCtrl.totalSaldoDeudor = 0;
        balanceSituacionCtrl.totalSaldoAcreedor = 0;
        balanceSituacionCtrl.estiloSuma = '';
        balanceSituacionCtrl.estiloSaldos = '';

        if($cookieStore.get('user') == undefined){
            $location.path("/");
        } else {
            balanceSituacionCtrl.user = $cookieStore.get('user');
        }
        if(balanceSituacionCtrl.user.rolCode == 'CL01'){
            balanceSituacionCtrl.dataRequest.user_id = balanceSituacionCtrl.user.userId;
        }

        balanceSituacionCtrl.initCtrl = function(){
            balanceSituacionCtrl.detalleBalance = false;
            balanceSituacionCtrl.balanceDetalle = {};
            angular.element('#div-loading').show();
            var dataRequest = {start : '', size : ''};
            balanceSituacionService.getBalancesSituacion(balanceSituacionCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == 'OK'){
                        balanceSituacionCtrl.balances = result.data;

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

        balanceSituacionCtrl.verDetalleBalance = function(balanceId){
            angular.element('#div-loading').show();
            balanceSituacionService.getBalanceSituacionDetalle(balanceId, balanceSituacionCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    balanceSituacionCtrl.detalleBalance = true;
                    if(result.status == 'OK'){
                        balanceSituacionCtrl.balanceDetalle = result.data;
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

    }]);*/
