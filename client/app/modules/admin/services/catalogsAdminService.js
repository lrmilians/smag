myAdmin.factory('catalogsAdminService', ['$http','$q','PROPERTIES','PROPERTIES_ADMIN' , function ($http, $q, PROPERTIES, PROPERTIES_ADMIN) {

    var catalogsAdminService = {
        getRoles : getRoles
    };

    function getRoles(dataRequest, token){
        var deferred = $q.defer();
        var wsGetRoles = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsGetRoles =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ADMIN.services.uriWebServiceGetRoles +
                '/token/' + token;
        } else {
            wsGetRoles =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ADMIN.services.uriWebServiceGetRoles +
                '/token/' + token;
        }

        $http.post(wsGetRoles, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    return catalogsAdminService;
}]);

