myAdmin.factory('dashboardService', ['$http','$q','PROPERTIES','PROPERTIES_ESIGNATURE', function ($http, $q, PROPERTIES, PROPERTIES_ESIGNATURE) {

    var dashboardService = {
        getDocStat : getDocStat
        /*getNotifications : getNotifications,
        setReadNotification : setReadNotification,
        getProccess : getProccess,
        getApplications : getApplications,
        getDateSince : getDateSince*/
    };


    function getDocStat(dataRequest, token){
        var deferred = $q.defer();
        var wsGetDocStat = '';
        if(PROPERTIES.serverConn.port !== ''){
            wsGetDocStat =  PROPERTIES.serverConn.server + ":" +
                PROPERTIES.serverConn.port + "/" +
                PROPERTIES_ESIGNATURE.services.uriWebServiceGetDocStat +
                '/token/' + token;
        } else {
            wsGetDocStat =  PROPERTIES.serverConn.server +
                "/" + PROPERTIES_ESIGNATURE.services.uriWebServiceGetDocStat +
                '/token/' + token;;
        }

        $http.post(wsGetDocStat, dataRequest)
            .success(function(data, status, headers, config) {
                deferred.resolve(data);
            })
            .error(function (data, status, headers, config)	{
                deferred.reject(data);
            });

        return deferred.promise;
    }



    /*function getDateSince(fecha, split){
        var values = fecha.split(split);
        var dia = values[2];
        var mes = values[1];
        var ano = values[0];

        var fecha_hoy = new Date();
        var ahora_ano = fecha_hoy.getYear();
        var ahora_mes = fecha_hoy.getMonth() + 1;
        var ahora_dia = fecha_hoy.getDate();

        var edad = (ahora_ano + 1900) - ano;
        if (ahora_mes < mes) {
            edad--;
        }
        if ((mes == ahora_mes) && (ahora_dia < dia)) {
            edad--;
        }

        if (edad > 1900) {
            edad -= 1900;
        }

        var meses = 0;
        if (ahora_mes > mes)
            meses = ahora_mes - mes;
        if (ahora_mes < mes)
            meses = 12 - (mes - ahora_mes);
        if (ahora_mes == mes && dia > ahora_dia)
            meses = 11;

        var dias = 0;
        if (ahora_dia > dia)
            dias = ahora_dia - dia;
        if (ahora_dia < dia) {
            var ultimoDiaMes = new Date(ahora_ano, ahora_mes, 0);
            dias = ultimoDiaMes.getDate() - (dia - ahora_dia);
        }

        if(meses == 1 && edad == 0){
            meses = meses - 1;
            dias = dias + 1;
        }
        var resultDia = '';
        if(dias == 0 && meses == 0 && edad == 0){
            resultDia = "Hoy";
        } else {
            if(dias == 1){
                resultDia = "un día";
            } else {
                if(dias == 0){
                    resultDia = "";
                } else {
                    resultDia = dias + " días";
                }
            }
        }
        var resultMes = '';
        if(meses == 0){
            resultMes = "";
        } else {
            resultDia = " y " + resultDia;
            if(meses == 1){
                resultMes = "un mes ";
            } else {
                resultMes = meses + " meses ";
            }
        }
        var resultAnio = '';
        if(edad == 0){
            resultAnio = "";
        } else {
            if(meses == 0){
                resultDia = " y " + resultDia;
            }
            if(dias == 0){
                resultMes = " y " + resultMes;
            }
            if(edad == 1){
                resultAnio = "un año ";
            } else {
                resultAnio = edad + " años ";
            }
        }
        var result = {day : resultDia, month : resultMes, year : resultAnio};

        return result;
    }*/

    return dashboardService;
}]);


