myAdmin.factory('catalogsEsignatureService', ['$http','$q','PROPERTIES','PROPERTIES_ESIGNATURE' , function ($http, $q, PROPERTIES, PROPERTIES_ESIGNATURE) {

    var catalogsEsignatureService = {
        getSRIValidations : getSRIValidations
    };

    function getSRIValidations(token){
        var deferred = $q.defer();
        var wsGetSRIValidations = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsGetSRIValidations =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ESIGNATURE.services.uriWebServiceGetSRIValidations +
                '/token/' + token;
        } else {
            wsGetSRIValidations =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ESIGNATURE.services.uriWebServiceGetSRIValidations +
                '/token/' + token;
        }

        $http.get(wsGetSRIValidations).success(
            function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    return catalogsEsignatureService;
}]);

