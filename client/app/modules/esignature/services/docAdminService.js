myAdmin.factory('esignAdminService', ['$http','$q','PROPERTIES','PROPERTIES_ESIGNATURE', '$upload', function ($http, $q, PROPERTIES, PROPERTIES_ESIGNATURE, $upload) {

    var esignAdminService = {
        addDoc : addDoc
    };

    function addDoc(dataRequest, file, token){
        var deferred = $q.defer();
        var wsAddDoc = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsAddDoc =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ESIGNATURE.services.uriWebServiceAdddoc +
                '/token/' + token;
        } else {
            wsAddDoc =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ESIGNATURE.services.uriWebServiceAdddoc +
                '/token/' + token;
        }

        $upload.upload({
                url: wsAddDoc,
                file: file,
                data: dataRequest
            })
            .success(function (data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    return esignAdminService;
}]);


myAdmin.factory('billAdminService', ['$http','$q','PROPERTIES','PROPERTIES_ESIGNATURE', '$upload', function ($http, $q, PROPERTIES, PROPERTIES_ESIGNATURE, $upload) {

    var billAdminService = {
        getBills : getBills,
        processBill : processBill,
        /*setKey : setKey,
        delKey : delKey*/
    };

    function getBills(dataRequest, token){
        var deferred = $q.defer();
        var wsGetBills = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsGetBills =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ESIGNATURE.services.uriWebServiceBills +
                '/token/' + token;
        } else {
            wsGetBills =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ESIGNATURE.services.uriWebServiceBills +
                '/token/' + token;
        }

        $http.post(wsGetBills, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function processBill(dataRequest, token){
        var deferred = $q.defer();
        var wsProcessBill = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsProcessBill =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ESIGNATURE.services.uriWebServiceProcessDocs +
                '/token/' + token;
        } else {
            wsProcessBill =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ESIGNATURE.services.uriWebServiceProcessDocs +
                '/token/' + token;
        }

        $http.post(wsProcessBill, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    /*function setKey (dataRequest, file, token){
        var deferred = $q.defer();
        var wsSetKey = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsSetKey =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ESIGNATURE.services.uriWebServiceKey +
                '/token/' + token;
        } else {
            wsSetKey =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ESIGNATURE.services.uriWebServiceKey +
                '/token/' + token;
        }
        if(!file){
            $http.post(wsSetKey, dataRequest)
                .success(function(data, status, headers, configura) {
                    deferred.resolve(data);
                })
                .error(function (data, status, headers, configura)	{
                    deferred.reject(data);
                });
        } else {
            $upload.upload({
                url: wsSetKey,
                file: file,
                data: dataRequest
            })
                .success(function (data, status, headers, configura) {
                    deferred.resolve(data);
                })
                .error(function (data, status, headers, configura)	{
                    deferred.reject(data);
                });
        }


        return deferred.promise;
    }

    function delKey(dataRequest, token){
        var deferred = $q.defer();
        var wsDelKey = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsDelKey =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ESIGNATURE.services.uriWebServiceKeyDel +
                "/token/" + token;
        } else {
            wsDelKey =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ESIGNATURE.services.uriWebServiceKeyDel +
                "/token/" + token;
        }

        $http.post(wsDelKey, dataRequest)
            .success(function(data, status, headers, configura) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, configura)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }*/

    return billAdminService;
}]);
