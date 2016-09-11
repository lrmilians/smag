'use strict';

var myContabilidad = angular.module('contabilidad',['ngRoute']);

myContabilidad.config(function ($routeProvider) {
    $routeProvider
        .when('/diario', {
            templateUrl: 'client/app/modules/contabilidad/views/diario.html',
            controller: 'diarioCtrl',
            activetab: 'contabilidad'
        })
        .when('/balance-comprobacion', {
            templateUrl: 'client/app/modules/contabilidad/views/balance-comprobacion.html',
            controller: 'balanceComprobacionCtrl',
            activetab: 'contabilidad'
        })
        .when('/balance-situacion', {
            templateUrl: 'client/app/modules/contabilidad/views/balance-situacion.html',
            controller: 'balanceSituacionCtrl',
            activetab: 'contabilidad'
        })
        .otherwise({reditrectTo: "/"});
});

myContabilidad.constant('PROPERTIES_CONTABILIDAD', {
    "services": {
        "uriWebServiceGetCuentas": "contabilidad/cuenta/cuentas",
        "uriWebServiceGetAsientos": "contabilidad/diario/asientos",
        "uriWebServiceAddAsiento": "contabilidad/diario/asiento",
        "uriWebServiceDelAsiento": "contabilidad/diario/delasiento",
        "uriWebServiceGetBalanceComprobacion": "contabilidad/balances/comprobacion",
        "uriWebServiceGetDetalleCuenta": "contabilidad/cuenta/getcuenta",
        "uriWebServiceCierreContable": "contabilidad/diario/cierrecontable",
        "uriWebServiceGetBalancesSituacion": "contabilidad/balances/situacion",
        "uriWebServiceGetBalanceSituacionDetalle": "contabilidad/balances/situaciondetalle",
    },
    "regularExpression" : {
        "passport" : /^[a-zA-Z0-9_-]{4,15}$/,
        "phone" : /^[0-9_-]{6,15}$/,
        "name" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\é\í\ó\ú\ñ\Ñ\â\ê\î\ô\û\.\_\-\s]{3,60}$/,
        "entity" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\â\ê\î\ô\û\.\-\_\,\;\s]{5,80}$/,
        "description" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\â\ê\î\ô\û\-\_\,\;\.\#\%\&\(\)\=\?\¿\¡\!\{\}\[\]\s]{10,500}$/,
        "number2Digits" : /^[0-9]{1,2}$/,
        "number3Digits" : /^[0-9]{1,3}$/,
        "number" : /^[0-9]+(\.[0-9]{1,2})?$/,
        "acronym" : /^[a-zA-Z0-9_-]{3,25}$/,
        "university" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\â\ê\î\ô\û\-\_\,\;\.\s]{2,60}$/,
    },
    "tipoCuentas" : [
        {id : 0, descripcion : "DEBE"},
        {id : 1, descripcion : "HABER"}
    ]
});


