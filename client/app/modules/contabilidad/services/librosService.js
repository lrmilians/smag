myAdmin.factory('diarioService', ['$http','$q','PROPERTIES','PROPERTIES_CONTABILIDAD', '$upload', function ($http, $q, PROPERTIES, PROPERTIES_CONTABILIDAD) {

    var diarioService = {
        getAsientos : getAsientos,
        addAsiento : addAsiento,
        delAsiento : delAsiento,
        cierreContable : cierreContable
    };

    function getAsientos(dataRequest, token){
        var deferred = $q.defer();
        var wsGetAsientos = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsGetAsientos =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_CONTABILIDAD.services.uriWebServiceGetAsientos +
                '/token/' + token;
        } else {
            wsGetAsientos =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_CONTABILIDAD.services.uriWebServiceGetAsientos +
                '/token/' + token;
        }

        $http.post(wsGetAsientos, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function addAsiento(dataRequest, token){
        var deferred = $q.defer();
        var wsAddAsiento = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsAddAsiento =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_CONTABILIDAD.services.uriWebServiceAddAsiento +
                '/token/' + token;
        } else {
            wsAddAsiento =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_CONTABILIDAD.services.uriWebServiceAddAsiento +
                '/token/' + token;
        }

        $http.post(wsAddAsiento, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function delAsiento(dataRequest, token){
        var deferred = $q.defer();
        var wsDelAsiento = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsDelAsiento =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_CONTABILIDAD.services.uriWebServiceDelAsiento +
                '/token/' + token;
        } else {
            wsDelAsiento =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_CONTABILIDAD.services.uriWebServiceDelAsiento +
                '/token/' + token;
        }

        $http.post(wsDelAsiento, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function cierreContable(dataRequest, token){
        var deferred = $q.defer();
        var wsCierreContable = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsCierreContable =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_CONTABILIDAD.services.uriWebServiceCierreContable +
                '/token/' + token;
        } else {
            wsCierreContable =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_CONTABILIDAD.services.uriWebServiceCierreContable +
                '/token/' + token;
        }

        $http.post(wsCierreContable, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    return diarioService;
}]);


myAdmin.factory('mayorService', ['$http','$q','PROPERTIES','PROPERTIES_CONTABILIDAD', '$upload', function ($http, $q, PROPERTIES, PROPERTIES_CONTABILIDAD) {

    var mayorService = {
        getBalanceComprobacion : getBalanceComprobacion,
        getDetalleCuenta :  getDetalleCuenta
    };

    function getBalanceComprobacion(token){
        var deferred = $q.defer();
        var wsGetBalanceComprobacion = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsGetBalanceComprobacion =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_CONTABILIDAD.services.uriWebServiceGetBalanceComprobacion +
                '/token/' + token;
        } else {
            wsGetBalanceComprobacion =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_CONTABILIDAD.services.uriWebServiceGetBalanceComprobacion +
                '/token/' + token;
        }

        $http.get(wsGetBalanceComprobacion).success(
            function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function getDetalleCuenta(cuentaId, token){
        var deferred = $q.defer();
        var wsGetDetalleCuenta = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsGetDetalleCuenta =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_CONTABILIDAD.services.uriWebServiceGetDetalleCuenta +
                '/id/' + cuentaId + '/token/' + token;
        } else {
            wsGetDetalleCuenta =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_CONTABILIDAD.services.uriWebServiceGetDetalleCuenta +
                '/id/' + cuentaId + '/token/' + token;
        }

        $http.get(wsGetDetalleCuenta).success(
            function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    return mayorService;
}]);
