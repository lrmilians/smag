myAdmin.factory('keyAdminService', ['$http','$q','PROPERTIES','PROPERTIES_ESIGNATURE', '$upload', function ($http, $q, PROPERTIES, PROPERTIES_ESIGNATURE, $upload) {

    var keyAdminService = {
        getKeys : getKeys,
        setKey : setKey,
        delKey : delKey
    };

    function getKeys(token){
        var deferred = $q.defer();
        var wsGetKeys = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsGetKeys =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ESIGNATURE.services.uriWebServiceKeys+
                '/token/' + token;
        } else {
            wsGetKeys =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ESIGNATURE.services.uriWebServiceKeys +
                '/token/' + token;
        }

        $http.get(wsGetKeys).success(
            function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function setKey (dataRequest, file, token){
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
                .success(function(data, status, headers, config) {
                    deferred.resolve(data);
                })
                .error(function (data, status, headers, config)	{
                    deferred.reject(data);
                });
        } else {
            $upload.upload({
                url: wsSetKey,
                file: file,
                data: dataRequest
            })
                .success(function (data, status, headers, config) {
                    deferred.resolve(data);
                })
                .error(function (data, status, headers, config)	{
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
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    return keyAdminService;
}]);
