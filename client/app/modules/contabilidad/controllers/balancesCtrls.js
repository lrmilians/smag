'use strict';
/**
 * @ngdoc function
 * @name prometeoApp.controller:balanceComprobacionCtrl,
 *
 * @description
 * # balanceComprobacionCtrl,
 * Controller of the esignatureApp
 */

myAdmin.controller("balanceComprobacionCtrl", ['catalogsContabilidadService','mayorService','$scope','$modal','dialogs','$location','$log','$cookieStore', '$translate',
    function(catalogsContabilidadService,mayorService,$scope,$modal,dialogs,$location,$log,$cookieStore,$translate) {

        var balanceComprobacionCtrl = this;

        balanceComprobacionCtrl.totalSumaDebe = 0;
        balanceComprobacionCtrl.totalSumaHaber = 0;
        balanceComprobacionCtrl.totalSaldoDeudor = 0;
        balanceComprobacionCtrl.totalSaldoAcreedor = 0;
        balanceComprobacionCtrl.estiloSuma = '';
        balanceComprobacionCtrl.estiloSaldos = '';

        if($cookieStore.get('user') == undefined){
            $location.path("/");
        } else {
            balanceComprobacionCtrl.user = $cookieStore.get('user');
        }
        if(balanceComprobacionCtrl.user.rolCode == 'CL01'){
            balanceComprobacionCtrl.dataRequest.user_id = balanceComprobacionCtrl.user.userId;
        }

        balanceComprobacionCtrl.initCtrl = function(){
            balanceComprobacionCtrl.detalleCuenta = false;
            balanceComprobacionCtrl.detallesCuenta = {};
            angular.element('#div-loading').show();
            var dataRequest = {start : '', size : ''};
            mayorService.getBalanceComprobacion(balanceComprobacionCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == 'OK'){
                        balanceComprobacionCtrl.balance = result.data.data;
                        var total_suma_debe = 0;
                        var total_suma_haber = 0;
                        var total_saldo_deudor = 0;
                        var total_saldo_acreedor = 0;

                        for(var i in balanceComprobacionCtrl.balance){
                            total_suma_debe = total_suma_debe + parseFloat(balanceComprobacionCtrl.balance[i].suma_debe);
                            total_suma_haber = total_suma_haber + parseFloat(balanceComprobacionCtrl.balance[i].suma_haber);
                            switch (true){
                                case parseFloat(balanceComprobacionCtrl.balance[i].saldo) > 0:
                                    balanceComprobacionCtrl.balance[i].saldo_deudor = parseFloat(balanceComprobacionCtrl.balance[i].saldo);
                                    balanceComprobacionCtrl.balance[i].saldo_acreedor = 0;
                                    break;
                                case balanceComprobacionCtrl.balance[i].saldo < 0:
                                    balanceComprobacionCtrl.balance[i].saldo_deudor = 0;
                                    balanceComprobacionCtrl.balance[i].saldo_acreedor = parseFloat(balanceComprobacionCtrl.balance[i].saldo);
                                    break;
                                default:
                                    balanceComprobacionCtrl.balance[i].saldo_deudor = 0;
                                    balanceComprobacionCtrl.balance[i].saldo_acreedor = 0;
                                    break;
                            }
                            total_saldo_deudor = total_saldo_deudor + parseFloat(balanceComprobacionCtrl.balance[i].saldo_deudor);
                            total_saldo_acreedor = total_saldo_acreedor + (parseFloat(balanceComprobacionCtrl.balance[i].saldo_acreedor) * -1);
                        }
                        balanceComprobacionCtrl.totalSumaDebe = total_suma_debe.toFixed(2);
                        balanceComprobacionCtrl.totalSumaHaber = total_suma_haber.toFixed(2);
                        balanceComprobacionCtrl.totalSaldoDeudor = total_saldo_deudor.toFixed(2);
                        balanceComprobacionCtrl.totalSaldoAcreedor = total_saldo_acreedor.toFixed(2);
                        switch (true){
                            case balanceComprobacionCtrl.totalSumaDebe === balanceComprobacionCtrl.totalSumaDebe:
                                balanceComprobacionCtrl.estiloSuma = 'success text-success';
                                break;
                            case balanceComprobacionCtrl.totalSumaDebe !== balanceComprobacionCtrl.totalSumaDebe:
                                balanceComprobacionCtrl.estiloSuma = 'danger text-danger';
                                break;
                        }
                        switch (true){
                            case balanceComprobacionCtrl.totalSaldoDeudor === balanceComprobacionCtrl.totalSaldoAcreedor:
                                balanceComprobacionCtrl.estiloSaldos = 'success text-success';
                                break;
                            case balanceComprobacionCtrl.totalSaldoDeudor !== balanceComprobacionCtrl.totalSaldoAcreedor:
                                balanceComprobacionCtrl.estiloSaldos = 'danger text-danger';
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
                });

        }

        balanceComprobacionCtrl.verDetalleCuenta = function(cuentaId){
            angular.element('#div-loading').show();
            mayorService.getDetalleCuenta(cuentaId, balanceComprobacionCtrl.user.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    balanceComprobacionCtrl.detalleCuenta = true;
                    if(result.status == 'OK'){
                        balanceComprobacionCtrl.detallesCuenta = result.data;
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

myAdmin.controller("balanceSituacionCtrl", ['catalogsContabilidadService','balanceSituacionService','$scope','$modal','dialogs','$location','$log','$cookieStore', '$translate',
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

    }]);
