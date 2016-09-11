myAdmin.factory('catalogsContabilidadService', ['$http','$q','PROPERTIES','PROPERTIES_CONTABILIDAD' , function ($http, $q, PROPERTIES, PROPERTIES_CONTABILIDAD) {

    var catalogsContabilidadService = {
        getCuentas : getCuentas
    };

    function getCuentas(token){
        var deferred = $q.defer();
        var wsGetCuentas = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsGetCuentas =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_CONTABILIDAD.services.uriWebServiceGetCuentas +
                '/token/' + token;
        } else {
            wsGetCuentas =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_CONTABILIDAD.services.uriWebServiceGetCuentas +
                '/token/' + token;
        }

        $http.get(wsGetCuentas).success(
            function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    return catalogsContabilidadService;
}]);

