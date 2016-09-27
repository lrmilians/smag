'use strict';
/**
 * @ngdoc function
 * @name prometeoApp.controller:userLoginCtrl,
 *                              userCtrl,
 *                              changePasswordDialogCtrl
 * @description
 * # userLoginCtrl,
 * # userCtrl,
 * # changePasswordDialogCtrl
 * Controller of the esignatureApp
 */
myAdmin.controller("userLoginCtrl", ['PROPERTIES_ADMIN','userAdminService','$scope','$modal','dialogs','$location','$log','$cookieStore', '$translate',
            function(PROPERTIES_ADMIN,userAdminService,$scope,$modal,dialogs,$location,$log,$cookieStore,$translate) {

    var userLoginCtrl = this;
    userLoginCtrl.remember = false;

    userLoginCtrl.login = function(){
      if ($scope.loginForm.$valid){

          $.getJSON('//freegeoip.net/json/?callback=?', function(data) {
              var dataRequest = {
                  username : userLoginCtrl.userId,
                  pwd : userLoginCtrl.userPassword,
                  remember : userLoginCtrl.remember,
                  ipaddress : data.ip
              };
              angular.element('#div-loading').show();
              userAdminService.getUserLogin(dataRequest)
                  .then(function(result){
                      angular.element('#div-loading').hide();
                      switch (result.code) {
                          case '0501': //Login exitoso.
                              var user = {
                                  active : result.data.active ? true : false,
                                  token : result.data.token,
                                  username : result.data.username,
                                  userId : result.data.user_id,
                                  identification : result.data.identification,
                                  email : result.data.email,
                                  firstName : result.data.first_name,
                                  lastName : result.data.last_name,
                                  rolName : result.data.rol_name,
                                  rolCode : result.data.rol_code,
                                  isConnected : true,
                                  urlStart : 'dashboard'};
                              $scope.connectedUser = user;
                              $cookieStore.put('user', user);
                              $cookieStore.put('login', true);
                              switch (user.rolCode){
                                  case 'AD01':
                                      userLoginCtrl.mainMenu = PROPERTIES_ADMIN.mainMenu[1].data;
                                      break;
                                  case 'CL01':
                                      userLoginCtrl.mainMenu = PROPERTIES_ADMIN.mainMenu[2].data;
                                      break;
                              }
                              $cookieStore.put('mainMenu', userLoginCtrl.mainMenu);
                              $scope.$parent.initCtrl();
                              $location.path('/dashboard');
                              // dialogs.notify(undefined, result.message);
                              break;
                          case '0502': //Contraseña incorrecta.
                              dialogs.error(undefined, result.message);
                              break;
                          case '0503': //Cuenta inactiva.
                              dialogs.error(undefined, result.message);
                              break;
                          case '0504': //Cuenta bloqueda.
                              dialogs.error(undefined, result.message);
                              break;
                          case '0305': //Contraseña incorrecta.
                          case '0505': //Contraseña incorrecta.
                              //dialogs.error(undefined, result.message);
                              dialogs.error(undefined, 'Contraseña incorrecta');
                              break;
                      }
                  }).catch(function(data){
                      angular.element('#div-loading').hide();
                      dialogs.error('Error', data.message);

                  });
          });

      }
    }

}]);

myAdmin.controller("userCtrl", ['PROPERTIES_ADMIN','userAdminService','$scope','$modal','dialogs','$location','$log','$cookieStore', '$translate',
    function(PROPERTIES_ADMIN,userAdminService,$scope,$modal,dialogs,$location,$log,$cookieStore,$translate) {

    var userCtrl = this;

    userCtrl.initCtrl = function(){
        angular.element('#div-loading').show();
        userCtrl.userToken = $cookieStore.get('user').token;
        userAdminService.getUserProfile(userCtrl.userToken)
            .then(function(result){
                angular.element('#div-loading').hide();
                if(result.status == 'OK'){
                    userCtrl.userIdentification = result.data.identification;
                    userCtrl.userName = result.data.username;
                    userCtrl.email = result.data.email;
                    userCtrl.firstName = result.data.first_name;
                    userCtrl.lastName = result.data.last_name;
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

    userCtrl.setUserProfile = function() {
        if ($scope.userProfileForm.$valid) {
            var dataRequest = {
                first_name : userCtrl.firstName,
                last_name : userCtrl.lastName
            }
            angular.element('#div-loading').show();
            userAdminService.setUserProfile(dataRequest, userCtrl.userToken)
                .then(function(result){
                    if(result.status == "OK"){
                        var userCookie = $cookieStore.get('user');
                        userCookie.firstName = userCtrl.firstName;
                        userCookie.lastName = userCtrl.lastName;
                        $cookieStore.remove('user');
                        $cookieStore.put('user',userCookie);
                        $translate('DATA_SAVE').then(function (msg) {
                            dialogs.notify(undefined, msg);
                        });
                        $scope.$parent.initCtrl();
                        userCtrl.initCtrl();
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
                            dialogs.error('Error', data.message);
                        }
                    }
                });
        }
    }

    userCtrl.changePassword = function(){
        var dlg = dialogs.create('client/app/modules/admin/views/dialog-form/form-change-password.html','changePasswordDialogCtrl',{
            userToken : $scope.connectedUser.token, userIdentification: $scope.connectedUser.identification},'lg');
        dlg.result.then(function(result){
            if(result.status == "OK"){
                $scope.$parent.logout(true);
            }
        },function(){
            if(angular.equals($scope.name,''))
                $scope.name = 'You did not enter in your name!';
        });
    }

}]);

myAdmin.controller("changePasswordDialogCtrl",function(PROPERTIES_ADMIN,userAdminService,$scope,$modalInstance,data,$translate,dialogs){

    $scope.cancel = function(){
        $modalInstance.dismiss('Canceled');
    };

    $scope.checkPassword = function () {
        $scope.dontMatch = $scope.newPassword1 !== $scope.newPassword2;
    };

    $scope.submitForm = function(){
        if ($scope.changePasswordForm.$valid && $scope.newPassword1 === $scope.newPassword2){
            var dataRequest = {
                identification : data.userIdentification,
                old : $scope.currentPassword,
                new : $scope.newPassword1
            };
            angular.element('#div-loading').show();
            userAdminService.changePassword(dataRequest, data.userToken)
                .then(function(result){
                    angular.element('#div-loading').hide();
                    if(result.status == "OK"){
                        dialogs.notify(undefined, result.message);
                        $modalInstance.close(result);
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
                            dialogs.error('Error', data.message);
                        }
                    }
                });
        }
    };

})
