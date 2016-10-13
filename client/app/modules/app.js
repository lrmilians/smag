'use strict';

angular
    .module('smagApp', [
        'ngAnimate',
        'ngAria',
        'ngCookies',
        'ngMessages',
        'ngResource',
        'ngRoute',
        'ngSanitize',
        'ngTouch',
        'pascalprecht.translate',
        'angularFileUpload',
        'angularUtils.directives.dirPagination',
        'ui.bootstrap',
        'dialogs.main',
        'esignature',
        'admin',
        'contabilidad',
        'inventario',
        'clientes'
    ])

    .constant('PROPERTIES', {
        "serverConn" :	{
            "server" : "https://u1510desktop64x/smag",
            // "server" : "https://vmu1510.lrconsultor.com/smag",
            "port"	: ""
        },
        "maetroTablas" :	{
            datosGenerales : '01',
            categoriasProducto : '100',
            tiposProducto : '101',
            marcasProducto : '102',
            modelosProducto : '103',
            unidadesMedida : '104',
            ivas : '08',
            estadosProducto : '107',
            icesCompra : '09',
            icesVenta : '09',
            tiposIdentificacion : '302',
            condicionesPago: '306',
        },
         "expresionesRegulares" : {
             "codigoProducto" : /^[a-zA-Z0-9\.\_\-\s]{1,100}$/,
             "codigoBarrasProducto" : /^[a-zA-Z0-9\.\_\-\s]{1,100}$/,
             "nombreProducto" : /^[a-zA-Z0-9\.\_\-\s]{1,250}$/,
             "referenciaProducto" : /^[a-zA-Z0-9\.\_\-\s]{0,200}$/,
             "descripcionProducto" : /^[a-zA-Z0-9\(\)\#\@\!\<\>\?\-\$\%\_\,\;\.\s]{0,10000}$/,

             "texto" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\(\)\&\#\@\!\<\>\?\-\$\%\_\,\;\.\s]{0,10000}$/,
             "decimal186" : /^[0-9]{1,12}(\.[0-9]{2,6})$/,
             "decimal126" : /^[0-9]{1,6}(\.[0-9]{2,6})$/,
             "decimal102" : /^[0-9]{1,8}(\.[0-9]{2,2})$/,
             "decimal166" : /^[0-9]{1,10}(\.[0-9]{2,6})$/,
             "decimal62" : /^[0-9]{1,4}(\.[0-9]{2,2})$/,


            "pasaporte" : /^[a-zA-Z0-9_-]{4,15}$/,
            "telefono" : /^[0-9_-]{6,15}$/,
            "nombre" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\é\í\ó\ú\ñ\Ñ\â\ê\î\ô\û\.\_\-\s]{3,60}$/,
            "entidad" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\â\ê\î\ô\û\.\-\_\,\;\s]{5,80}$/,
            "descripcion" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\â\ê\î\ô\û\-\_\,\;\.\s]{10,500}$/,
            "num2Digitos" : /^[0-9]{1,2}$/,
            "num3Digitos" : /^[0-9]{1,3}$/,
            "siglas" : /^[a-zA-Z0-9_-]{3,25}$/,
            "universidad" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\â\ê\î\ô\û\-\_\,\;\.\s]{2,60}$/,
        },
        "services": {
            "uriWebServiceGetCatalogos": "admin/tablas/catalogos",
            "uriWebServiceExisteCampos": "admin/utils/existecampos",

        }
    })


    .run(function($rootScope, $location, $cookieStore, dialogs){
      var templateUrl = [
        'client/app/modules/admin/views/dashboard.html',
        'client/app/modules/admin/views/user-profile.html',
        'client/app/modules/esignature/views/keys.html',
        'client/app/modules/esignature/views/bills.html',
        'client/app/modules/contabilidad/views/inv-mtto.html',
        'client/app/modules/contabilidad/views/balance-comprobacion.html',
        'client/app/modules/contabilidad/views/balance-situacion.html',
      ];
      var templateUrlAdmin = [
        'client/app/modules/admin/views/users.html',
        'client/app/modules/admin/views/qualifications-byadmin.html',
        'client/app/modules/admin/views/magazines-byadmin.html',
        'client/app/modules/admin/views/comments.html',
        'client/app/modules/contabilidad/views/inv-mtto.html',
        'client/app/modules/contabilidad/views/balance-comprobacion.html',
        'client/app/modules/contabilidad/views/balance-situacion.html',
      ];
      $rootScope.$on('$routeChangeStart', function(even, next, current){

        if($cookieStore.get('login') == false || $cookieStore.get('login') == null){

          for(var i in templateUrl){
            if(templateUrl[i] == next.templateUrl){
                dialogs.error('Error', 'No tiene permisos de acceso a la URL solicitada.');
                $location.path('/');
            }
          }
        } else {
          var user = $cookieStore.get('user');
          if(user.rolCode == "AD01"){

          }
          if(user.rolCode == "GU01"){
            for(var i in templateUrlAdmin){
              if(templateUrlAdmin[i] == next.templateUrl){
                dialogs.error('Error', 'No tiene permisos de acceso a la URL solicitada.');
                $location.path('/dashboard');
              }
            }
          }
          if (next.templateUrl == 'client/app/views/main.html'){
            $location.path('/dashboard');
          }
        }
      })
    })

    .config(function($httpProvider) {
        var headers = {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Access-Control-Allow-Origin' : '*',
          'Access-Control-Allow-Methods' : 'POST, GET, OPTIONS, PUT'
        };
    })

    .config(['dialogsProvider',function(dialogsProvider){
        dialogsProvider.useBackdrop('static');
        dialogsProvider.useEscClose(false);
        dialogsProvider.useCopy(false);
        dialogsProvider.setSize('md');
    }])

    .config (function ($translateProvider) {
        $translateProvider.useStaticFilesLoader({
            files: [{
                prefix: 'client/app/languages/adminmodule/locale-',
                suffix: '.json'
            }]
        });
        $translateProvider.preferredLanguage('es_ES');
        $translateProvider.useCookieStorage();
        $translateProvider.useSanitizeValueStrategy('escaped');
    })

  .config(function ($routeProvider) {
    $routeProvider
      .when('/', {
        templateUrl: 'client/app/views/main.html',
        controller: 'userLoginCtrl',
        activetab: 'start'
    })
    .otherwise({
      redirectTo: '/'
    });
  });


