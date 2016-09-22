'use strict';

var myInventario = angular.module('inventario',['ngRoute']);

myInventario.config(function ($routeProvider) {
    $routeProvider
        .when('/inv-mtto', {
            templateUrl: 'client/app/modules/inventario/views/inv-mtto.html',
            controller: 'invMttoCtrl',
            activetab: 'inventario'
        })

});

myInventario.constant('PROPERTIES_INVENTARIO', {
    "services": {
        //CRUD PRODUCTOS
        "uriWebServiceGetProductos": "inventario/productos/producto",
        "uriWebServiceSetProducto": "inventario/productos/setproducto",
        "uriWebServiceDelProducto": "inventario/productos/delproducto",
        "uriWebServiceGetCatalogos": "admin/tablas/catalogos",


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


