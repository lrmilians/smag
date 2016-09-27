myAdmin.factory('tablasService', ['$http','$q','PROPERTIES','PROPERTIES_ADMIN', '$upload', function ($http, $q, PROPERTIES, PROPERTIES_ADMIN) {

    var tablasService = {
        getTablas : getTablas,
        getTabla : getTabla,
        setTablas : setTablas,
        delTabla : delTabla
    };

    function getTablas(dataRequest, token){
        var deferred = $q.defer();
        var wsGetTablas =  PROPERTIES.serverConn.server + "/" + PROPERTIES_ADMIN.services.uriWebServiceGetTablas + '/token/' + token;

        $http.post(wsGetTablas, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function getTabla(dataRequest, token){
        var deferred = $q.defer();
        var wsGetTabla =  PROPERTIES.serverConn.server + "/" + PROPERTIES_ADMIN.services.uriWebServiceGetTabla + '/token/' + token;

        $http.post(wsGetTabla, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function setTablas(dataRequest, token){
        var deferred = $q.defer();
        var wsSetTablas =  PROPERTIES.serverConn.server + "/" + PROPERTIES_ADMIN.services.uriWebServiceSetTablas + '/token/' + token;

        $http.post(wsSetTablas, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function delTabla(dataRequest, token){
        var deferred = $q.defer();
        var wsDelTabla =  PROPERTIES.serverConn.server + "/" + PROPERTIES_ADMIN.services.uriWebServiceDelTabla + '/token/' + token;

        $http.post(wsDelTabla, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }


    return tablasService;

}]);
