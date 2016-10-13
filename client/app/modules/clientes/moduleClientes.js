'use strict';

var myClientes = angular.module('clientes',['ngRoute']);

myClientes.config(function ($routeProvider) {
    $routeProvider
        .when('/clientes-mtto', {
            templateUrl: 'client/app/modules/clientes/views/clientes-mtto.html',
            controller: 'clientesCtrl',
            activetab: 'clientes'
        })

});

myInventario.constant('PROPERTIES_CLIENTES', {
    "services": {
        //CRUD CLIENTES
        "uriWebServiceGetClientes": "maecliente/clientes/cliente",
        "uriWebServiceSetCliente": "maecliente/clientes/setproducto",
        "uriWebServiceDelCliente": "maecliente/clientes/delcliente",

    }
});


