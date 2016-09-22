myInventario.factory('invMttoService', ['$http','$q','PROPERTIES','PROPERTIES_INVENTARIO', '$upload', function ($http, $q, PROPERTIES, PROPERTIES_INVENTARIO) {

    var invMttoService = {
        getProductos : getProductos,
        setProducto : setProducto,
        delProducto : delProducto,
        getCatalogos : getCatalogos
    };

    function getProductos(dataRequest, token){
        var deferred = $q.defer();
        var wsGetProductos =  PROPERTIES.serverConn.server + "/" + PROPERTIES_INVENTARIO.services.uriWebServiceGetProductos + '/token/' + token;

        $http.post(wsGetProductos, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function setProducto(dataRequest, token){
        var deferred = $q.defer();
        var wsSetProducto =  PROPERTIES.serverConn.server + "/" + PROPERTIES_INVENTARIO.services.uriWebServiceSetProducto + '/token/' + token;

        $http.post(wsSetProducto, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function delProducto(dataRequest, token){
        var deferred = $q.defer();
        var wsDelProducto =  PROPERTIES.serverConn.server + "/" + PROPERTIES_INVENTARIO.services.uriWebServiceDelProducto + '/token/' + token;

        $http.post(wsDelProducto, dataRequest)
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
        var wsGetCatalogos =  PROPERTIES.serverConn.server + "/" + PROPERTIES_INVENTARIO.services.uriWebServiceGetCatalogos + '/token/' + token;

        $http.post(wsGetCatalogos, tablas)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }


    return invMttoService;
}]);
