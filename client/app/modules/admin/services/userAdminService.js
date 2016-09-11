myAdmin.factory('userAdminService', ['$http','$q','PROPERTIES','PROPERTIES_ADMIN', function ($http, $q, PROPERTIES, PROPERTIES_ADMIN) {

    var userLoginService = {
        getUserLogin : getUserLogin,
        getUserLogout : getUserLogout,
        getUserConfirm : getUserConfirm,
        getUserActived : getUserActived,
        getUserProfile : getUserProfile,
        setUserProfile : setUserProfile,
        changePassword : changePassword,
        getUsers : getUsers,
        addUser : addUser,
        getUserExists : getUserExists
    };

    function getUserLogin(dataRequest){
        var deferred = $q.defer();
        var wsUserLogin = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsUserLogin =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ADMIN.services.uriWebServiceUserLogin;
        } else {
            wsUserLogin =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ADMIN.services.uriWebServiceUserLogin;
        }

        $http.post(wsUserLogin, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function getUserLogout(token){
        var deferred = $q.defer();
        var wsUserLogout = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsUserLogout =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ADMIN.services.uriWebServiceUserLogout +
                '/token/' + token;
        } else {
            wsUserLogout =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ADMIN.services.uriWebServiceUserLogout +
                '/token/' + token;
        }

        $http.get(wsUserLogout).success(
            function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function getUserConfirm(userName, token){
        var deferred = $q.defer();
        var wsUserConfirm =  PROPIEDADES.propiedades.server + ":" +
            PROPIEDADES.propiedades.port + "/" +
            PROPERTIES_ADMIN.services.uriWebServiceUserConfirm +
            "/username/" + userName + "/serie/" + token;

        $http.get(wsUserConfirm).success(
            function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });
        return deferred.promise;
    }

    function getUserActived(userName, token){
        var deferred = $q.defer();
        var wsUserActived =  PROPIEDADES.propiedades.server + ":" +
            PROPIEDADES.propiedades.port + "/" +
            PROPERTIES_ADMIN.services.uriWebServiceUserActived +
            '/username/' + userName;

        $http.get(wsUserActived).success(
            function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });
        return deferred.promise;
    }


    function getUserProfile(token){
        var deferred = $q.defer();
        var wsUserProfile = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsUserProfile =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ADMIN.services.uriWebServiceUserProfile +
                '/token/' + token;
        } else {
            wsUserProfile =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ADMIN.services.uriWebServiceUserProfile +
                '/token/' + token;
        }

        $http.get(wsUserProfile).success(
            function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function setUserProfile(dataRequest, token){
        var deferred = $q.defer();
        var wsUserProfile = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsUserProfile =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ADMIN.services.uriWebServiceUserProfile +
                '/token/' + token;
        } else {
            wsUserProfile =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ADMIN.services.uriWebServiceUserProfile +
                '/token/' + token;
        }

        $http.post(wsUserProfile, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function changePassword(dataRequest, token){
        var deferred = $q.defer();
        var wsChangePassword = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsChangePassword =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ADMIN.services.uriWebServiceUserChangePassword +
                '/token/' + token;
        } else {
            wsChangePassword =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ADMIN.services.uriWebServiceUserChangePassword +
                '/token/' + token;
        }

        $http.post(wsChangePassword, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }


    function getUsers(dataRequest, token){
        var deferred = $q.defer();
        var wsGetUsers = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsGetUsers =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ADMIN.services.uriWebServiceGetUsers +
                '/token/' + token;
        } else {
            wsGetUsers =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ADMIN.services.uriWebServiceGetUsers +
                '/token/' + token;
        }

        $http.post(wsGetUsers, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function addUser(dataRequest, token){
        var deferred = $q.defer();
        var wsAddUser = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsAddUser =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ADMIN.services.uriWebServiceUser +
                '/token/' + token;
        } else {
            wsAddUser =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ADMIN.services.uriWebServiceUser +
                '/token/' + token;
        }

        $http.post(wsAddUser, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }

    function getUserExists(dataRequest){
        var deferred = $q.defer();
        var wsUserExists = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsUserExists =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ADMIN.services.uriWebServiceUserExists;
        } else {
            wsUserExists =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ADMIN.services.uriWebServiceUserExists;
        }

        $http.post(wsUserExists, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }



    return userLoginService;
}]);
