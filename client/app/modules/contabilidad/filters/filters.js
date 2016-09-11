myContabilidad.filter('cuenta', function () {
    return function (cuenta) {
        if (!cuenta || cuenta == '--') { return ''; }

        return ("(" + cuenta + ")").trim();
    };
});