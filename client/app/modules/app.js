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
        'inventario'

    ])

    .constant('PROPERTIES', {
      "serverConn" :	{
        "server" : "https://u1510desktop64x/smag",
        //"server" : "https://vmu1510.lrconsultor.com/smag",
        "port"	: ""
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
    //    dialogsProvider.setSize('md');
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
