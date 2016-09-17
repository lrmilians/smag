'use strict';

var myAdmin = angular.module('admin',['ngRoute']);

myAdmin.config(function ($routeProvider) {
    $routeProvider
        .when('/user-register', {
            templateUrl: 'modules/admin/views/user-register.html',
            controller: 'userRegisterCtrl',
            activetab: 'register'
        })
        .when('/user-profile', {
            templateUrl: 'client/app/modules/admin/views/user-profile.html',
            controller: 'userCtrl',
            activetab: 'user-profile'
        })
        .when('/dashboard', {
            templateUrl: 'client/app/modules/admin/views/dashboard.html',
            controller: 'dashboardCtrl',
            activetab: 'start'
        })
        .otherwise({reditrectTo: "/"});
});


myAdmin.constant('PROPERTIES_ADMIN', {
    "regularExpression" : {
        "userName" : /^[a-z0-9_-]{6,15}$/,
        "passport" : /^[a-zA-Z0-9_-]{4,15}$/,
        "phone" : /^[0-9_-]{6,15}$/,
        "name" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\é\í\ó\ú\ñ\Ñ\â\ê\î\ô\û\.\_\-\s]{3,60}$/,
        "entity" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\â\ê\î\ô\û\.\-\_\,\;\s]{5,80}$/,
        "description" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\â\ê\î\ô\û\-\_\,\;\.\s]{10,500}$/,
        "number2Digits" : /^[0-9]{1,2}$/,
        "number3Digits" : /^[0-9]{1,3}$/,
        "acronym" : /^[a-zA-Z0-9_-]{3,25}$/,
        "university" : /^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\â\ê\î\ô\û\-\_\,\;\.\s]{2,60}$/,
    },
    "services" : {
        "uriWebServiceUserExists" : "auth/user/userexists",
        "uriWebServiceUserLogin" : "auth/auth/login",
        "uriWebServiceUserLogout" : "auth/auth/logout",
        "uriWebServiceUserProfile" : "auth/user/userprofile",
        "uriWebServiceUserChangePassword" : "auth/auth/changepwd",
        "uriWebServiceGetUsers" : "auth/user/users",
        "uriWebServiceGetRoles" : "auth/rol/roles",
        "uriWebServiceUser" : "auth/user/user",
    },
    "mainMenu" : [
        {
            "id" : 0,  //NO LOGIN
            "data" : [
                {href : '#/', clic : '', title : 'Inicio', activetab : 'start'},
                {href : '#/faq', clic : '', title : 'FAQ', activetab : 'faq'},
                {href : '#/contact', clic : '', title : 'Contáctenos', activetab : 'contact'}
            ]
        },
        {
            "id" : 1,  //ADMINISTRADOR
            "data" : [
                {href : '#/dashboard', clic : '', title : 'Panel', activetab : 'start'},
             /*   {href : '#/status-application', clic : '', title : 'menuStatusApp', activetab : 'status-application'},
                {href : '#/notifications', clic : '', title : 'menuNotifications', activetab : 'notifications'},
                {href : '#/bases-application', clic : '', title : 'menuHelp', activetab : 'help'},*/
             /*   {href : '#/user-profile', clic : '', title : 'Perfil usuario', activetab : 'user-profile'},
                {href : '#/keys', clic : '', title : 'Llaves', activetab : 'key'},
                {href : '#/bills', clic : '', title : 'Facturas', activetab : 'bill'},
                {href : '#/contabilidad', clic : '', title : 'Contabilidad', activetab : 'contabilidad', glyphicon : 'glyphicon-dashboard',
                    submenu : [
                        {href : '#/diario', title : 'Libro Diario', glyphicon : 'glyphicon-folder-open'},
                        {href : '#/balance-comprobacion', title : 'Balance Comprobación', glyphicon : 'glyphicon-folder-open'},
                        {href : '#/balance-situacion', title : 'Balances Situación', glyphicon : 'glyphicon-folder-open'},
                    ]
                },*/
                {href : '#/inventario', clic : '', title : 'Inventario', activetab : 'inventario', glyphicon : 'glyphicon-dashboard',
                    submenu : [
                        {href : '#/inv-mtto', title : 'Mantenimiento', glyphicon : 'glyphicon-folder-open'},
                    ]
                },
            ]
        },
        {
            "id" : 2,   //CLIENTE
            "data" : [
                {href : '#/dashboard', clic : '', title : 'Panel', activetab : 'start'},
                {href : '#/user-profile', clic : '', title : 'Perfil usuario', activetab : 'user-profile'},
                {href : '#/bills', clic : '', title : 'Facturas', activetab : 'bill'}
            ]
        },
        {
            "id" : 3,   //ADMINISTRADOR FUNCIONAL
            "data" : [
                {href : '#/dashboard', clic : '', title : 'PROCESOS', activetab : 'start', glyphicon : 'glyphicon-dashboard',
                    submenu : [
                        {href : '#/dashboard', title : 'Postulaciones', glyphicon : 'glyphicon-folder-open'},
                        {href : '#/qualifications-byadmin', title : 'Calificación', glyphicon : 'glyphicon-pencil'},
                        {href : '#/validations-byadmin', title : 'Convalidación de errores', glyphicon : 'glyphicon-repeat'}
                    ]
                },
                {href : '', clic : '', title : 'CALIFICACIONES', activetab : 'qualifications', glyphicon : 'glyphicon-pencil',
                    submenu : [
                        {href : '', title : 'Calificaciones', glyphicon : 'glyphicon-pencil'},
                        {href : '#/comments', title : 'Comentarios de calificación', glyphicon : 'glyphicon-comment'}
                    ]
                },
                {href : '#/users', clic : '', title : 'menuUsers', activetab : 'users', glyphicon : 'glyphicon-cog'},
                {href : '#/magazines-byadmin', clic : '', title : 'REVISTAS', activetab : 'magazines', glyphicon : 'glyphicon-duplicate'},
                {href : '#/user-profile', clic : '', title : 'menuUserProfile', activetab : 'user-profile', glyphicon : 'glyphicon-user'}
            ]
        },
    ]



});




