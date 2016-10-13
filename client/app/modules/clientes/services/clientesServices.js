myClientes.factory('clientesService', ['$http','$q','PROPERTIES','PROPERTIES_CLIENTES', function ($http, $q, PROPERTIES, PROPERTIES_CLIENTES) {

    var clientesService = {
        getClientes : getClientes,
        setCliente : setCliente,
        existeCampos : existeCampos,
        getCatalogos : getCatalogos
    };

    function getClientes(dataRequest, token){
        var deferred = $q.defer();
        var wsGetClientes =  PROPERTIES.serverConn.server + "/" + PROPERTIES_CLIENTES.services.uriWebServiceGetClientes + '/token/' + token;

        $http.post(wsGetClientes, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function setCliente(dataRequest, token){
        var deferred = $q.defer();
        var wsSetCliente =  PROPERTIES.serverConn.server + "/" + PROPERTIES_CLIENTES.services.uriWebServiceSetCliente + '/token/' + token;

        $http.post(wsSetCliente, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function existeCampos(dataRequest, token){
        var deferred = $q.defer();
        var wsExisteCampos =  PROPERTIES.serverConn.server + "/" + PROPERTIES.services.uriWebServiceExisteCampos + '/token/' + token;

        $http.post(wsExisteCampos, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function getCatalogos(tablas, token){
        var deferred = $q.defer();
        var wsGetCatalogos =  PROPERTIES.serverConn.server + "/" + PROPERTIES.services.uriWebServiceGetCatalogos + '/token/' + token;

        $http.post(wsGetCatalogos, tablas)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }


    return clientesService;
}]);
