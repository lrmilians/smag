'use strict';

/*myEsignature.controller("parentController", function($rootScope){
    $rootScope.file = {};
});

myEsignature.controller("keysController", function($scope, $routeParams, $route, $location, keyFactory) {
    $scope.allKeys = [];
    $scope.key = {
        file: undefined,
        password: undefined,
        active: undefined
    };

    keyFactory.getAllKeys().success(function(data) {
        if (!Object.keys(data).length) {
            $scope.response = "empty";
            $scope.allKeys = [];
        } else {
            $scope.response = "filled";
            $scope.allKeys = data;
        }
    });
    
   
    
    $scope.deleteKey = function(key) {
        keyFactory.deleteKey(key).success(function(data) {
            $route.reload();
        });
    };
    
    

    //hacemos logout al usuario
    $scope.logout = function()
    {
        //eliminamos todas las sesiones
       
        //refrescamos con reload, al ver que no existen las sesiones
        //somo enviados al login
        $route.reload();
    }

});

myEsignature.controller("addKeyController", function($scope, $routeParams, $route, $location, keyFactory, $rootScope) {
    $scope.addKey= function(key) {
        if ($.isEmptyObject($rootScope.file)) {
            alert ("Seleccione un archivo llave.");
        }else if (key.password === undefined) {
            alert("Escriba la contraseña.");
        } else {
            keyFactory.getKeyByName($rootScope.file.name).success(function(data) {
                if(data.response === "noexit") {			
                    key.file = $rootScope.file;
                    keyFactory.addKey(key).success(function(data) {
                    $location.path("/esignature/keys");
                });
               } else {
                   alert ("Esta llave se encuentra en la base de datos");
               }
            });
        }
    };
});

myEsignature.controller("editKeyController", function($scope, $routeParams, $route, $location, keyFactory) {
    $scope.key = [];
    keyFactory.getKeyById($routeParams.id).success(function(data) {
        if (!Object.keys(data).length) {
            $location.path("/dashboard");
        } else {
            if (data.active === "1") $scope.active = true; else $scope.active = false;
            $scope.key = data;
        }
    });
    
    $scope.editKey = function(key) {
        if (key.active === true) key.active = "1"; else key.active = "0";
        keyFactory.editKey(key).success(function(data) {
            $location.path("/esignature/keys");
        });
    };
});*/



myEsignature.controller("billsController", ['billService','$scope','$routeParams','$route','$location','$rootScope', 'dialogs',
    function(billService, $scope, $routeParams, $route, $location, $rootScope, dialogs) {

    $scope.allBills = [];
    $scope.bill = {
        file: undefined,
        password: undefined,
        active: undefined
    };

    billService.getBills(25)
        .then(function(result){
           if(result.length == 0){
               $scope.response = "empty";
               $scope.allBills = [];
           } else {
               $scope.response = "filled";
               $scope.allBills = result;
           }
        }).catch(function(data){
            dialogs.error('Error', data);
        });

  
   /* $scope.deleteBill = function(bill) {
        billFactory.deleteBill(bill).success(function(data) {
            $route.reload();
        });
    };
    
    $scope.getMessagesByBillNameView = function(myname){
        billFactory.getMessagesByBillName(myname).success(function(data) {
            $scope.xmlName = myname;
            if (!Object.keys(data).length) {
                $scope.response = "emptyMessages";
                $scope.allMessages = [];
            } else {
                $scope.response = "filledMessages";
                $scope.allMessages = data;
            }
        });
    };
    
    $scope.addBill = function() {
        if ($.isEmptyObject($rootScope.file)) {
            alert ("Seleccione una factura.");
        }else {
            billFactory.getBillByName($rootScope.file.name).success(function(data) {
                if(data.response === "noexit") {
                    $scope.bill.file = $rootScope.file;
                    billFactory.addBill($scope.bill).success(function(data) {
                    $location.path("/esignature/bills");
                });
               } else {
                   alert ("Esta factura se encuentra en la base de datos");
               }
            });
        }
    };
    
    $scope.processBill = function(bill) {
        $('.busy-indicator').show();
        var path_domain = "facturas_electronicas_xml";
        billFactory.processBill(bill, path_domain).success(function(data) {
            $('.busy-indicator').hide();
            if (data.respuesta !== undefined) {
                alert(data.respuesta);
            }  
            $route.reload();
        });
    };*/

}]);

/*
myEsignature.controller("retentionsController", function ($scope, $routeParams, $route, $location, retentionsFactory) {
    $scope.namedb = ($location.search()).namedb;
    $scope.allRetentions = [];
    $scope.retention = {};

    retentionsFactory.getAllRetentions($scope.namedb).success(function (data) {
        if (!Object.keys(data).length) {
            $scope.response = "empty";
            $scope.allRetentions = [];
        } else {
            $scope.response = "filled";
            $scope.allRetentions = data;
        }
    });
    
    $scope.getMessagesByRetentionNameView = function(myname){
        retentionsFactory.getMessagesByRetentionName($scope.namedb, myname).success(function(data) {
            $scope.xmlName = myname;
            if (!Object.keys(data).length) {
                $scope.response = "emptyMessages";
                $scope.allMessages = [];
            } else {
                $scope.response = "filledMessages";
                $scope.allMessages = data;
            }
        });
    };

});

myEsignature.controller("creditnotesController", function ($scope, $routeParams, $route, $location, creditnotesFactory, $rootScope) {
    $scope.allCreditnotes = [];
    $scope.creditnote = {
        file: undefined,
        password: undefined,
        active: undefined
    };
    $scope.retention = {};

    creditnotesFactory.getAllCreditnotes().success(function (data) {
        if (!Object.keys(data).length) {
            $scope.response = "empty";
            $scope.allCreditnotes = [];
        } else {
            $scope.response = "filled";
            $scope.allCreditnotes = data;
        }
    });
    
    $scope.getMessagesByCreditnoteNameView = function(myname){
        creditnotesFactory.getMessagesByCreditnoteName(myname).success(function(data) {
            $scope.xmlName = myname;
            if (!Object.keys(data).length) {
                $scope.response = "emptyMessages";
                $scope.allMessages = [];
            } else {
                $scope.response = "filledMessages";
                $scope.allMessages = data;
            }
        });
    };
    
    $scope.addCreditnote = function() {
        if ($.isEmptyObject($rootScope.file)) {
            alert ("Seleccione una factura.");
        }else {
            creditnotesFactory.getCreditnoteByName($rootScope.file.name).success(function(data) {
                if(data.response === "noexit") {
                    $scope.creditnote.file = $rootScope.file;
                    creditnotesFactory.addCreditnote($scope.creditnote).success(function(data) {
                    $location.path("/esignature/creditnotes");
                });
               } else {
                   alert ("Esta nota de credito se encuentra en la base de datos");
               }
            });
        }
    };
    
    $scope.processCreditnote = function(creditnote) {
        $('.busy-indicator').show();
        var path_domain = "notas_credito_electronicas_xml";
        creditnotesFactory.processCreditnote(creditnote, path_domain).success(function(data) {
            $('.busy-indicator').hide();
            if (data.respuesta !== undefined) {
                alert(data.respuesta);
            }  
            $route.reload();
        });
    };

});


myEsignature.controller("debitnotesController", function ($scope, $routeParams, $route, $location, debitnotesFactory) {
    $scope.namedb = ($location.search()).namedb;
    $scope.allDebitnotes = [];
    $scope.debitnote = {};

    debitnotesFactory.getAllDebitnotes($scope.namedb).success(function (data) {
        if (!Object.keys(data).length) {
            $scope.response = "empty";
            $scope.getAllDebitnotes = [];
        } else {
            $scope.response = "filled";
            $scope.getAllDebitnotes = data;
        }
    });
    
    $scope.getMessagesByDebitnoteNameView = function(myname){
        debitnotesFactory.getMessagesByDebitnoteName($scope.namedb, myname).success(function(data) {
            $scope.xmlName = myname;
            if (!Object.keys(data).length) {
                $scope.response = "emptyMessages";
                $scope.allMessages = [];
            } else {
                $scope.response = "filledMessages";
                $scope.allMessages = data;
            }
        });
    };

});

myEsignature.controller("remitionguidesController", function ($scope, $routeParams, $route, $location, remitionguidesFactory) {
    $scope.namedb = ($location.search()).namedb;
    $scope.allRemitionguides = [];
    $scope.remitionguide = {};

    remitionguidesFactory.getAllRemitionguides($scope.namedb).success(function (data) {
        if (!Object.keys(data).length) {
            $scope.response = "empty";
            $scope.allRemitionguides = [];
        } else {
            $scope.response = "filled";
            $scope.allRemitionguides = data;
        }
    });
    
    $scope.getMessagesByRemitionguideNameView = function(myname){
        remitionguidesFactory.getMessagesByRemitionguideName($scope.namedb, myname).success(function(data) {
            $scope.xmlName = myname;
            if (!Object.keys(data).length) {
                $scope.response = "emptyMessages";
                $scope.allMessages = [];
            } else {
                $scope.response = "filledMessages";
                $scope.allMessages = data;
            }
        });
    };

});

myEsignature.controller("pacMailerController", function ($scope, $routeParams, $route, $location, mailerFactory) {
    $scope.values = [];

    mailerFactory.getConfigurationValues().success(function (data) {
        if (!Object.keys(data).length) {
            $scope.response = "empty";
            $scope.getConfigurationValues = [];
        } else {
            $scope.response = "filled";
            $scope.getConfigurationValues = data;
            
            $scope.from = $scope.getConfigurationValues[0].from;
            $scope.subject = $scope.getConfigurationValues[0].subject;
            $scope.body = $scope.getConfigurationValues[0].body;
            $scope.host = $scope.getConfigurationValues[0].host;
            $scope.port = $scope.getConfigurationValues[0].port;
            $scope.auth = $scope.getConfigurationValues[0].auth;
            $scope.username = $scope.getConfigurationValues[0].username;
            $scope.password = $scope.getConfigurationValues[0].password;
            $scope.ssl = $scope.getConfigurationValues[0].ssl;
            $scope.path = $scope.getConfigurationValues[0].path;
        }
    });
    
    $scope.getChecked = function()
    {
        if($scope.auth == 'true')
            return true;
        if($scope.auth == '1')
            return true;
        return false;
    }
    
    $scope.getCheckedSSL = function()
    {
        if($scope.ssl == 'true')
            return true;
        if($scope.ssl == '1')
            return true;
        return false;
    }
    
    $scope.saveChanges = function() {
        var data = {};
        data.from = $scope.from;
        data.subject = $scope.subject;
        data.body = $scope.body;
        data.host = $scope.host;
        data.port = $scope.port;
        data.auth = $scope.auth;
        data.username = $scope.username;
        data.password = $scope.password;
        data.ssl = $scope.ssl;
        data.path = $scope.path;
        
    mailerFactory.setConfigurationValues(data).success(function () {
       $route.reload();
    });
    
    };
});

myEsignature.controller("unsubscribeController", function ($http, $scope, $routeParams, $route, $location, unsubscribeFactory) {

    $scope.email = ($location.search()).email;

    unsubscribeFactory.getEmail($scope.email).success(function (data) {
        if (!Object.keys(data).length) {
            $scope.showCancel = false;
        } else {
            $scope.showCancel = true;
        }
    });

    $scope.addNewEmail = function () {
        if (typeof $scope.reason == 'undefined'){
            alert('Por favor seleccione una razón.');
        } else {
            unsubscribeFactory.addNewEmail($scope.email, $scope.reason).success(function (data) {
                if (!Object.keys(data).length) {
                    $scope.response = "emailAdded";
                } else {
                    $scope.response = "emailNotAdded";
                }
                $route.reload();
            });
        }
    };

});

myEsignature.controller("subscribeController", function ($http, $scope, $routeParams, $route, $location, subscribeFactory) {

    $scope.email = ($location.search()).email;

    subscribeFactory.getAllEmails().success(function (data) {
        if (!Object.keys(data).length) {
            $scope.emailList = [];
        } else {
            $scope.emailList = data;
        }
    });

    $scope.subscribeEmail = function (email) {
        subscribeFactory.subscribeEmail(email).success(function (data) {
            
            if (!Object.keys(data).length) {
                $scope.response = "emailEnabled";
                
            } else {
                $scope.response = "emailNotEnabled";
            }
            $route.reload();
        });
    };

});*/

myEsignature.config(function ($routeProvider) {
   
    $routeProvider.when("/esignature/keys", {
            templateUrl: "client/app/modules/esignature/views/keys/index.html",
            controller: "keysController"
        })
        .when("/esignature/keys/edit/:id", {
            templateUrl: "client/app/modules/esignature/views/keys/edit.html",
            controller: "editKeyController"
        })
        .when("/esignature/keys/add", {
            templateUrl: "client/app/modules/esignature/views/keys/add.html",
            controller: "addKeyController"
        })
        
        .when("/esignature/bills", {
            templateUrl: "client/app/modules/esignature/views/bills/index.html",
            controller: "billsController"
        })
        .when("/esignature/bills/add", {
            templateUrl: "client/app/modules/esignature/views/bills/add.html",
            controller: "billsController"
        })
        .when("/esignature/retentions", {
            templateUrl: "client/app/modules/esignature/views/retentions/index.html",
            controller: "retentionsController"
        })
        .when("/esignature/creditnotes", {
            templateUrl: "client/app/modules/esignature/views/creditnotes/index.html",
            controller: "creditnotesController"
        })
        .when("/esignature/creditnotes/add", {
            templateUrl: "client/app/modules/esignature/views/creditnotes/add.html",
            controller: "creditnotesController"
        })
        .when("/esignature/debitnotes", {
            templateUrl: "client/app/modules/esignature/views/debitnotes/index.html",
            controller: "debitnotesController"
        })
        .when("/esignature/remitionguides", {
            templateUrl: "client/app/modules/esignature/views/remitionguides/index.html",
            controller: "remitionguidesController"
        })
        .when("/esignature/pacmailer", {
            templateUrl: "client/app/modules/esignature/views/pacmailer/index.html",
            controller: "pacMailerController"
        })
        .when("/esignature/unsubscribe", {
            templateUrl: "client/app/modules/esignature/views/pacmailer_unsubscribe/index.html",
            controller: "unsubscribeController"
        })
        .when("/esignature/subscribe", {
            templateUrl: "client/app/modules/esignature/views/pacmailer_subscribe/index.html",
            controller: "subscribeController"
        })
        .otherwise({reditrectTo: "/"});
});