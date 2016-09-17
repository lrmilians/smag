myInventario.factory('invMttoService', ['$http','$q','PROPERTIES','PROPERTIES_INVENTARIO', '$upload', function ($http, $q, PROPERTIES, PROPERTIES_INVENTARIO) {

    var invMttoService = {
        getBalancesSituacion : getBalancesSituacion,
        getBalanceSituacionDetalle  : getBalanceSituacionDetalle
    };

    function getBalancesSituacion(token){
        var deferred = $q.defer();
        var wsGetBalancesSituacion = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsGetBalancesSituacion =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_INVENTARIO.services.uriWebServiceGetBalancesSituacion +
                '/token/' + token;
        } else {
            wsGetBalancesSituacion =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_INVENTARIO.services.uriWebServiceGetBalancesSituacion +
                '/token/' + token;
        }

        $http.get(wsGetBalancesSituacion).success(
            function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function getBalanceSituacionDetalle(balanceId, token){
        var deferred = $q.defer();
        var wsGetBalanceSituacionDetalle = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsGetBalanceSituacionDetalle =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_INVENTARIO.services.uriWebServiceGetBalanceSituacionDetalle +
                '/id/' + balanceId + '/token/' + token;
        } else {
            wsGetBalanceSituacionDetalle =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_INVENTARIO.services.uriWebServiceGetBalanceSituacionDetalle +
                '/id/' + balanceId + '/token/' + token;
        }

        $http.get(wsGetBalanceSituacionDetalle).success(
            function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }



    return invMttoService;
}]);
