'use strict';
/**
 * @ngdoc function
 * @name prometeoApp.controller:mainCtrl
 * @description
 * # mainCtrl
 * Controller of the esignatureApp
 */
myAdmin.controller("mainCtrl", ['PROPERTIES','PROPERTIES_ADMIN','userAdminService','$scope','$modal','dialogs','$location','$log','$route','$cookieStore', '$translate',
            function(PROPERTIES,PROPERTIES_ADMIN,userAdminService,$scope,$modal,dialogs,$location,$log,$route,$cookieStore,$translate) {

    var mainCtrl = this;
    mainCtrl.remember = false;
    $scope.mainMenu = [];
    $scope.$route = $route;

    $scope.baseURL = PROPERTIES.serverConn.server;
    if(PROPERTIES.serverConn.port != ""){
        $scope.baseURL = PROPERTIES.serverConn.server + ':' + PROPERTIES.serverConn.port;
    }

    $scope.cleanDataUser = function(cookie){
        $scope.connectedUser = {
            active : '',
            token : '',
            username : '',
            userId : '',
            identification : '',
            email : '',
            firstName : '',
            lastName : '',
            rolName : '',
            rolCode : '',
            isConnected : false,
            urlStart : ''};
        if(cookie){
            $cookieStore.remove('user');
        }
        $cookieStore.put('login', false);
    }

    if ($cookieStore.get('user') != undefined) {
        $scope.connectedUser = $cookieStore.get('user');
        $scope.mainMenu = [];
        $scope.mainMenu = $cookieStore.get('mainMenu');
    } else {
        $scope.mainMenu = PROPERTIES_ADMIN.mainMenu[0].data;
        $cookieStore.put('mainMenu', $scope.mainMenu);
        $scope.cleanDataUser(false);
    }

    $scope.initCtrl = function(){
       /* console.log($scope.connectedUser);
        if ($scope.connectedUser.active == '') {
            $scope.logout(true);
        }*/
        if ($cookieStore.get('user') != undefined) {
            $scope.connectedUser = $cookieStore.get('user');
            $scope.mainMenu = [];
            $scope.mainMenu = $cookieStore.get('mainMenu');
        }
    }

    $scope.logout = function(sessionExpired){
        $scope.mainMenu = PROPERTIES_ADMIN.mainMenu[0].data;
        if(sessionExpired){
            $scope.cleanDataUser(true);
            $location.path('/');
        } else {
            angular.element('#div-loading').show();
            userAdminService.getUserLogout($scope.connectedUser.token)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.code == '0505'){
                        dialogs.notify(undefined, result.message);
                        $scope.cleanDataUser(true);
                        $location.path('/');
                    }
                }).catch(function(data){
                    angular.element('#div-loading').hide();
                    switch (data.status){
                        case null:
                            dialogs.error('Error', "null");
                            break;
                        case 'OFF':
                            dialogs.notify(undefined, data.message);
                            $scope.cleanDataUser(true);
                            $location.path('/');
                            break;
                        default:
                            dialogs.error('Error', data.status);
                            break;
                    }
                });
        }
    }


                /*mainCtrl.confirm = function(){
                    userLoginService.getUserActived($routeParams.username)
                        .then(function(data){
                          if (data.status != true){
                              userLoginService.getUserConfirm($routeParams.username,$routeParams.token)
                                  .then(function(data){
                                      if(data.status == "OK"){
                                          mainCtrl.message = "userloginText6";
                                          mainCtrl.messageClass = "info";
                                      } else {
                                          mainCtrl.message = "userloginText9";
                                          mainCtrl.messageClass = "danger";
                                      }
                                  }).catch(function(data){
                                      mainCtrl.message = "userloginText7";
                                      mainCtrl.messageClass = "danger";
                                  });
                          } else {
                              mainCtrl.message = "userloginText8";
                              mainCtrl.messageClass = "info";
                          }
                        }).catch(function(data){
                            mainCtrl.message = "userloginText7";
                            mainCtrl.messageClass = "danger";
                        });
                }

                mainCtrl.gotToLogin = function(){
                    $location.path('/');
                }*/

}]);

