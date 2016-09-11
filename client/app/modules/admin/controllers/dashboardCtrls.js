'use strict';
/**
 * @ngdoc function
 * @name prometeoApp.controller:
 *                                  dashboardCtrl,
 *                                  applicDialog,
 *                                  searchApplicDialogCtrl,
 *                                  educationDetailDialogCtrl
 *                                  previousContactDialogCtrl
 * @description
 * # dashboardCtrl,
 * # applicDialog,
 * # searchApplicDialogCtrl
 * # educationDetailDialogCtrl
 * # previousContactDialogCtrl
 * Controller of the prometeoApp
 */
myAdmin.controller("dashboardCtrl", ['dashboardService','userAdminService','catalogsAdminService','$scope','$modal','dialogs','$location','$cookieStore','$translate',
            function(dashboardService,userAdminService,catalogsAdminService,$scope,$modal,dialogs,$location,$cookieStore,$translate) {

    var dashboardCtrl = this;
    dashboardCtrl.currentPage = 1;
    dashboardCtrl.pageSize = 15;
    dashboardCtrl.totalRecords = 0;
    dashboardCtrl.advanceSearch = false;

    dashboardCtrl.dataRequest = {};

    $scope.scrollStyle = $scope.scrollStyle2 = {};
    $scope.scrollTotalRecords = $scope.scrollTotalRecords2 = 8;
    $scope.scrollHeight = $scope.scrollHeight2 = 200;
    $scope.totalRecord = $scope.totalRecord2 = 0;

    dashboardCtrl.orderByField = 'pid';
    dashboardCtrl.reverseSort = false;

    if($cookieStore.get('user') == undefined){
        $location.path("/");
    } else {
        dashboardCtrl.user = $cookieStore.get('user');
    }

    dashboardCtrl.initCtrl = function(){
        var dataRequest = {start : '', size : ''};
        catalogsAdminService.getRoles(dataRequest, dashboardCtrl.user.token)
            .then(function(result){
                dashboardCtrl.roles = result.data;
            }).catch(function(data){
                dialogs.error('Error', data.error);
            });
        //console.log(dashboardCtrl.role);
        var indexValue = (dashboardCtrl.currentPage - 1) * dashboardCtrl.pageSize;
        dashboardCtrl.dataRequest.start = indexValue;
            switch(dashboardCtrl.user.rolCode){
                case "AD01":
                    dashboardCtrl.dataRequest = {
                        /* pid : "",
                         perfil : "",
                         nombre : "",
                         apellido : "",
                         paisNacimiento : "",
                         paisResidencia : "",
                         proceso : "",*/
                        start : 0,
                        size : dashboardCtrl.pageSize
                    };
                    dashboardCtrl.getUsers(dashboardCtrl.dataRequest);
                    break;
                case "CL01":
                    angular.element('#div-loading').show();
                    dashboardService.getDocStat({user_id : dashboardCtrl.user.userId}, dashboardCtrl.user.token)
                        .then(function(result){
                            angular.element('#div-loading').hide();
                            if(result.status == "OK"){
                                console.log(result);
                              dashboardCtrl.billCount = result.data.ebill_signing_bills.count;
                            }
                            if(result.status == "OFF") {
                                $translate('msgSessionExpired').then(function (msg) {
                                    dialogs.error('Error', msg);
                                });
                                $scope.$parent.logout(true);
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
                    break;
            }




    }

    dashboardCtrl.pageChangeHandler = function(newPageNumber){
        dashboardCtrl.currentPage = newPageNumber;
        var indexValue = (dashboardCtrl.currentPage - 1) * dashboardCtrl.pageSize;
        dashboardCtrl.dataRequest.start =  indexValue;
        dashboardCtrl.getUsers(dashboardCtrl.dataRequest);
    }
    dashboardCtrl.resetSearch = function(){
        dashboardCtrl.advanceSearch = false;
        switch(dashboardCtrl.user.rolCode) {
            case "AD01":
                dashboardCtrl.dataRequest = {
                    /*pid : "",
                     perfil : "",
                     nombre : "",
                     apellido : "",
                     paisNacimiento : "",
                     paisResidencia : "",
                     proceso : "",*/
                    start : 0,
                    size : dashboardCtrl.pageSize
                };
                dashboardCtrl.getUsers(dashboardCtrl.dataRequest);
                break;
            case "CL01":
                dashboardCtrl.dataRequest = {
                    /* pid : "",
                     perfil : "",
                     nombre : "",
                     apellido : "",
                     paisNacimiento : "",
                     paisResidencia : "",
                     proceso : "",*/
                    user_id : '',
                    start : 0,
                    size : dashboardCtrl.pageSize
                };
                dashboardCtrl.getBills(dashboardCtrl.dataRequest);
                break;
        }



    }


    dashboardCtrl.searchApplication = function(){
        angular.element('#div-loading').show();
        var dlg = dialogs.create('modules/admin/views/dialog-form/form-search-application.html','searchApplicDialogCtrl',{
            countrys : dashboardCtrl.countrys, searchCriteria : dashboardCtrl.dataRequest
        },'lg');
        dlg.result.then(function(result){
            dashboardCtrl.currentPage = 1;
            var indexValue = (dashboardCtrl.currentPage - 1) * dashboardCtrl.pageSize;
            dashboardCtrl.dataRequest.pid = result.pid;
            if(result.perfil != ""){
                dashboardCtrl.dataRequest.perfil = result.perfil.id;
            } else {
                dashboardCtrl.dataRequest.perfil = result.perfil;
            }
            dashboardCtrl.dataRequest.nombre = result.nombre;
            dashboardCtrl.dataRequest.apellido = result.apellido;
            if(result.paisNacimiento != ""){
                dashboardCtrl.dataRequest.paisNacimiento = result.paisNacimiento.id;
            } else {
                dashboardCtrl.dataRequest.paisNacimiento = result.paisNacimiento;
            }
            if(result.paisResidencia != ""){
                dashboardCtrl.dataRequest.paisResidencia = result.paisResidencia.id;
            } else {
                dashboardCtrl.dataRequest.paisResidencia = result.paisResidencia;
            }
            dashboardCtrl.dataRequest.inicio = indexValue;

            if(dashboardCtrl.dataRequest.pid != "" || dashboardCtrl.dataRequest.perfil != "" || dashboardCtrl.dataRequest.nombre != "" ||
                dashboardCtrl.dataRequest.apellido != "" || dashboardCtrl.dataRequest.paisNacimiento != "" || dashboardCtrl.dataRequest.paisResidencia != "" ||
                dashboardCtrl.dataRequest.proceso != ""){
                dashboardCtrl.advanceSearch = true;
            }
            dashboardCtrl.getApplications(dashboardCtrl.dataRequest);

        },function(){
            if(angular.equals($scope.name,''))
                $scope.name = 'You did not enter in your name!';
        });
    }


    dashboardCtrl.getUsers = function(dataRequest){
        angular.element('#div-loading').show();
        userAdminService.getUsers(dataRequest, dashboardCtrl.user.token)
            .then(function(result){
                angular.element('#div-loading').hide();
                if(result.status != "EMPTY"){
                    dashboardCtrl.users = result.data;
                    dashboardCtrl.totalRecords = result.total_rows;
                } else {
                    dashboardCtrl.users = [];
                    dashboardCtrl.totalRecords = 0;
                }
                if(result.status == "OFF") {
                    $translate('msgSessionExpired').then(function (msg) {
                        dialogs.error('Error', msg);
                    });
                    $scope.$parent.logout(true);
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


    dashboardCtrl.addUser = function(){
        var dlg = dialogs.create('client/app/modules/admin/views/dialog-form/form-user.html','userDialogCtrl',{
            userToken : dashboardCtrl.user.token,
            userNameLogin : dashboardCtrl.user.username,
            roles : dashboardCtrl.roles,
            action : -1,
            user : {}},'lg');
        dlg.result.then(function(result){
            if(result.status == "OK"){
                dashboardCtrl.initCtrl();
            }
        },function(){
            if(angular.equals($scope.name,''))
                $scope.name = 'You did not enter in your name!';
        });
    }

    dashboardCtrl.editUser = function(user){
        var dlg = dialogs.create('client/app/modules/admin/views/dialog-form/form-user.html','userDialogCtrl',{
            userToken : dashboardCtrl.user.token,
            userNameLogin : dashboardCtrl.user.username,
            roles : dashboardCtrl.roles,
            action : user.id,
            user : user
        },'lg');
        dlg.result.then(function(result){
            if(result.status == "OK"){
                dashboardCtrl.initCtrl();
            }
        },function(){
            if(angular.equals($scope.name,''))
                $scope.name = 'You did not enter in your name!';
        });
    }

    dashboardCtrl.viewUser = function(user){
        var dlg = dialogs.create('client/app/modules/admin/views/dialog-view/view-data-template.html','userDialogCtrl',{
            userToken : dashboardCtrl.user.token,
            userNameLogin : dashboardCtrl.user.username,
            roles : dashboardCtrl.roles,
            action : user.id,
            user : user
        },'lg');
        dlg.result.then(function(result){
            if(result.status == "OK"){
                dashboardCtrl.initCtrl();
            }
        },function(){
            if(angular.equals($scope.name,''))
                $scope.name = 'You did not enter in your name!';
        });
    }

}]);

myAdmin.controller("userDialogCtrl", function(PROPERTIES_ADMIN,userAdminService,$scope,$modalInstance,data,$translate,dialogs){

    $scope.patternUserName = PROPERTIES_ADMIN.regularExpression.userName;
    $scope.patternFirstName = $scope.patternLastName = $scope.patternCompany = PROPERTIES_ADMIN.regularExpression.name;
    $scope.action = data.action;

    $scope.title = "DATOS DEL USUARIO";
    $scope.glyphicon = "glyphicon-user";

    if(Object.keys(data.user).length !== 0){
        $scope.user = {
            userId : data.user.identification,
            userName : data.user.username,
            email : data.user.email,
            firstName : data.user.first_name,
            lastName : data.user.last_name,
            active : data.user.active,
            company : data.user.company,
            roles : data.roles
        };
        $scope.userNameOld = data.user.username;
        $scope.identificationOld = data.user.identification;
        for(var i in $scope.user.roles){
            if($scope.user.roles[i].id == data.user.rol_id){
                $scope.user.roleSelected = $scope.user.roles[i];
            }
        }
        $scope.dataEmpty = false;
        $scope.userNameLogin = data.userNameLogin;

        $scope.data = [
            {label: 'Identificacón ', value : data.user.identification},
            {label: 'Usuario ', value : data.user.username},
            {label: 'Email ', value : data.user.email},
            {label: 'Nombre ', value : data.user.first_name},
            {label: 'Apellido ', value : data.user.last_name},
            {label: 'Empresa ', value : data.user.company},
            {label: 'Rol ', value : $scope.user.roleSelected.description},
            {label: 'Activo ', value : data.user.active ? 'Si' : 'No'},
            {label: 'Logeado ', value : data.user.login ? 'Si' : 'No'},
            {label: 'Último Login ', value : data.user.last_login},
        ];
    } else {
        $scope.user = {
            userName : '',
            password : '',
            email : '',
            firstName : '',
            lastName : '',
            company : '',
            phone : '',
            active : true
        };
        $scope.userNameOld = "";
        $scope.emailOld = "";
        $scope.user.roles = data.roles;
        $scope.dataEmpty = true;
        $scope.userNameLogin = data.userNameLogin;
    }

    $scope.cancel = function(){
        $modalInstance.dismiss('Canceled');
    };

    $scope.submitForm = function(){
        if ($scope.userForm.$valid){
            var dataRequest = {
                 action : data.action,
                 username : $scope.user.userName,
                 identification : $scope.user.userId,
                 password : $scope.user.password,
                 email : $scope.user.email,
                 first_name : $scope.user.firstName,
                 last_name : $scope.user.lastName,
                 company : $scope.user.company,
                 phone : $scope.user.phone,
                 rol_id : $scope.user.roleSelected.id
            };
            if(data.action !== -1){
                dataRequest.active = $scope.user.active;
            }
            if($scope.userNameOld !== $scope.user.userName || $scope.identificationOld !== $scope.user.identification){
                angular.element('#div-loading').show();
                var dataRequestVerify = {username : dataRequest.username, identification : dataRequest.identification};
                userAdminService.getUserExists(dataRequestVerify)
                    .then(function(result){
                        angular.element('#div-loading').hide();
                        if (($scope.identificationOld !== $scope.user.identification && result.identification != true) || ($scope.userNameOld !== $scope.user.userName && result.username != true)) {
                            var message = 'usuario';
                            var prefix = '';
                            if ($scope.identificationOld !== $scope.user.identification && result.identification != true) {
                                var message = 'identificación';
                                if ($scope.userNameOld !== $scope.user.userName && result.username != true) {
                                    message = message + ' y el usuario ';
                                    prefix = 'n';
                                }
                            }
                            if ($scope.userNameOld !== $scope.user.userName && result.username != true) {
                                var message = 'usuario';
                                if ($scope.identificationOld !== $scope.user.identification && result.identification != true) {
                                    message = message + ' y la identificación ';
                                    prefix = 'n';
                                }
                            }
                            dialogs.notify(undefined,'El ' + message + ' esta' + prefix + ' en uso.');
                        } else {
                            $scope.setUser(dataRequest);
                        }
                    }).catch(function(data){
                        angular.element('#div-loading').hide();
                        dialogs.error(data.error);
                    });
            } else {
                $scope.setUser(dataRequest);
            }
        }
    };

    $scope.setUser = function(dataRequest){
        angular.element('#div-loading').show();
        userAdminService.addUser(dataRequest, data.userToken)
            .then(function(result){
                angular.element('#div-loading').hide();
                if(result.status == "OK"){
                    dialogs.notify(undefined, 'Datos salvados correctamente.');
                    $modalInstance.close(result);
                } else {
                    dialogs.error('Error', result.status);
                }
            }).catch(function(data){
                angular.element('#div-loading').hide();
                if(data.status == "OFF"){
                    $translate('msgSessionExpired').then(function (msg) {
                        dialogs.error('Error', msg);
                    });
                    $scope.$parent.logout(true);
                } else {
                    dialogs.error('Error', "codError:" + data.codError + " status: " + data.status);
                }
            });
    }
});