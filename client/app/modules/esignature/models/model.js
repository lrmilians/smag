'use strict';

/*myEsignature.factory("keyFactory", function($http, $location, $upload) {
    return {
        getAllKeys: function() {
            return $http({
                url: 'esignature/keys/getAllKeys',
                method: 'GET'
            });
        },
        addKey: function(key) {
           
            return $upload.upload({
                url: 'esignature/keys/addKey',
                file: key.file || null,
                data: {name : key.file.name.replace(/\s/g, "_"), password : key.password, active : key.active}
            }).success(function (data) {
                return data;
            });
         },
        editKey: function(key) {
            return $http({
                url: 'esignature/keys/editKey',
                method: 'POST',
                data: {id : key.id, name : key.name, password : key.password, active : key.active}
            });
        },
        deleteKey: function(key) {
            return $http({
                url: 'esignature/keys/deleteKey',
                method: 'POST',
                data: {id : key.id, name : key.name}
            });
        },
        getKeyById: function(id) {
            return $http({
                url: 'esignature/keys/getKeyById/' + id,
                method: 'GET'
            });
        },
        getKeyByName: function(name) {
            return $http({
                url: 'esignature/keys/getKeyByName',
                method: 'POST',
                data: {name : name}
            }).success(function (data) {
                return data;
            });
        }
    };
});*/

myEsignature.factory('billService', ['$http','$q', 'PROPERTIES', 'PROPERTIES_ESIGNATURE', '$location', '$upload',
    function ($http, $q, PROPERTIES, PROPERTIES_ESIGNATURE, $location, $upload) {

        var billService = {
            getBills : getBills
        };

        function getBills(limit){
            var deferred = $q.defer();
            if(PROPERTIES.serverConn.port !== ''){
                var wsBills =  PROPERTIES.serverConn.server + ":" +
                    PROPERTIES.serverConn.port + "/" +
                    PROPERTIES_ESIGNATURE.services.uriWebServiceBills +
                    "/limit/" + limit;
            } else {
                var wsBills =  PROPERTIES.serverConn.server +
                    "/" + PROPERTIES_ESIGNATURE.services.uriWebServiceBills +
                    "/limit/" + limit;
            }

            $http.get(wsBills).success(
                function(data, status, headers, config) {
                    deferred.resolve(data);
                }).
                error(function (data, status, headers, config)	{
                    deferred.reject(data);
                });

            return deferred.promise;
        }


    /*return {
        getAllBills: function(limit) {
            return $http({
                url: 'esignature/bills/facs/limit/' + limit,
                method: 'GET'
            });
        },
        addBill: function(bill) {
            return $upload.upload({
                url: 'esignature/bills/addBill',
                file: bill.file || null,
                data: {name : bill.file.name.replace(/\s/g, "_")}
            }).success(function (data) {
                return data;
            });
        },
        getMessagesByBillName: function(name) {
           return $http({
                url: 'esignature/bills/getMessagesByBillName/' + name,
                method: 'GET'
            });
        },
        editKey: function(key) {
            return $http({
                url: 'esignature/keys/editKey',
                method: 'POST',
                data: {id : key.id, name : key.name, password : key.password, active : key.active}
            });
        },
        deleteBill: function(bill) {
            return $http({
                url: 'esignature/bills/deleteBill',
                method: 'POST',
                data: {id : bill.id, name : bill.name}
            });
        },
        processBill: function(bill, path_domain) {
            return $http({
                url: 'esignature/esignatures/process',
                method: 'POST',
                data: {id : bill.id, name : bill.name, path_domain : path_domain}
            }).success(function (data) {
                return data;
            });
        },
        getBillById: function(id) {
            return $http({
                url: 'esignature/bills/getBillById/' + id,
                method: 'GET'
            });
        },
        getBillByName: function(name) {
            return $http({
                url: 'esignature/bills/getBillByName',
                method: 'POST',
                data: {name : name}
            }).success(function (data) {
                return data;
            });
        }
    };*/

    return billService;
}]);

/*myEsignature.factory("retentionsFactory", function ($http, $location) {
    return {
        getAllRetentions: function (namedb) {
            return $http({
                url: 'esignature/retentions/getAllRetentions/' + namedb,
                method: 'GET'
            });
        },
        getMessagesByRetentionName: function(namedb, myname) {
           return $http({
                url: 'esignature/retentions/getMessagesByRetentionName/' + namedb + '/' + myname,
                method: 'GET'
            });
        }
    };
});

myEsignature.factory("creditnotesFactory", function ($http, $upload) {
    return {
        getAllCreditnotes: function () {
            return $http({
                url: 'esignature/creditnotes/getAllCreditnotes',
                method: 'GET'
            });
        },
        addCreditnote: function(bill) {
            return $upload.upload({
                url: 'esignature/creditnotes/addCreditnote',
                file: bill.file || null,
                data: {name : bill.file.name.replace(/\s/g, "_")}
            }).success(function (data) {
                return data;
            });
        },
        getMessagesByCreditnoteName: function(myname) {
           return $http({
                url: 'esignature/creditnotes/getMessagesByCreditnoteName/' + myname,
                method: 'GET'
            });
        },
        processCreditnote: function(creditnote, path_domain) {
            return $http({
                url: 'esignature/esignatures/process',
                method: 'POST',
                data: {id : creditnote.id, name : creditnote.name, path_domain : path_domain}
            }).success(function (data) {
                return data;
            });
        },
        getCreditnoteByName: function(name) {
            return $http({
                url: 'esignature/creditnotes/getCreditnoteByName',
                method: 'POST',
                data: {name : name}
            }).success(function (data) {
                return data;
            });
        }
    };
});

myEsignature.factory("debitnotesFactory", function ($http, $location) {
    return {
        getAllDebitnotes: function (namedb) {
            return $http({
                url: 'esignature/debitnotes/getAllDebitNotes/' + namedb,
                method: 'GET'
            });
        },
        getMessagesByDebitnoteName: function(namedb, myname) {
           return $http({
                url: 'esignature/debitnotes/getMessagesByDebitnoteName/' + namedb + '/' + myname,
                method: 'GET'
            });
        }
    };
});

myEsignature.factory("remitionguidesFactory", function ($http, $location) {
    return {
        getAllRemitionguides: function (namedb) {
            return $http({
                url: 'esignature/remitionguides/getAllRemitionguides/' + namedb,
                method: 'GET'
            });
        },
        getMessagesByRemitionguideName: function(namedb, myname) {
           return $http({
                url: 'esignature/remitionguides/getMessagesByRemitionguideName/' + namedb + '/' + myname,
                method: 'GET'
            });
        }
    };
});

myEsignature.factory("mailerFactory", function ($http, $location) {
    return {
        getConfigurationValues: function () {
            return $http({
                url: 'esignature/pacmailer/getConfigurationValues',
                method: 'GET'
            });
        },
        setConfigurationValues: function (data) {
            return $http({
                url: 'esignature/pacmailer/setConfigurationValues/',
                method: 'POST',
                data: {from : data.from, subject : data.subject, body : data.body, host : data.host, 
                      port : data.port, auth : data.auth, username : data.username, password : data.password, ssl : data.ssl, path : data.path}
            });
        }
    };
});

//Mailer Unsubscribe
myEsignature.factory("unsubscribeFactory", function ($http, $location) {
    return {
        getEmail: function (email) {
            return $http({
                url: 'esignature/pacmailer_unsubscribe/getEmail',
                method: 'POST',
                data: { email: email}
            });
        },
        addNewEmail: function (email, reason) {
            return $http({
                url: 'esignature/pacmailer_unsubscribe/addNewEmail',
                method: 'POST',
                data: { email: email, reason: reason }
            }).success(function (data) {
                return data;
            });
        }
    };
});

//Mailer Subscribe
myEsignature.factory("subscribeFactory", function ($http) {
    return {
        getAllEmails: function () {
            return $http({
                url: 'esignature/pacmailer_subscribe/getAllEmails',
                method: 'POST'
            });
        },
        subscribeEmail: function (email) {
            return $http({
                url: 'esignature/pacmailer_subscribe/subscribeEmail',
                method: 'POST',
                data: { email: email }
            }).success(function (data) {
                return data;
            });
        }
    };
});*/