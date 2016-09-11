'use strict';

var myEsignature = angular.module('esignature',['ngRoute']);

myEsignature.config(function ($routeProvider) {
    $routeProvider
        .when('/keys', {
            templateUrl: 'client/app/modules/esignature/views/keys.html',
            controller: 'keyCtrl',
            activetab: 'key'
        })
        .when('/bills', {
            templateUrl: 'client/app/modules/esignature/views/bills.html',
            controller: 'billCtrl',
            activetab: 'bill'
        })
        .otherwise({reditrectTo: "/"});
});

myEsignature.constant('PROPERTIES_ESIGNATURE', {
    "services": {
        "uriWebServiceKeys": "esignature/key/keys",
        "uriWebServiceKey": "esignature/key/key",
        "uriWebServiceKeyFile": "esignature/key/keyfile",
        "uriWebServiceKeyDel": "esignature/key/keydel",
        "uriWebServiceGetSRIValidations": "esignature/srivalidation/srivalidations",
        "uriWebServiceBills": "esignature/bill/bills",
        "uriWebServiceAdddoc": "esignature/esign/adddoc",
        "uriWebServiceProcessDocs": "esignature/esign/processdocs",
        "uriWebServiceGetDocStat": "esignature/esign/getdocstat",
    },

});


