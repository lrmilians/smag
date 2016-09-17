myInventario.directive('mydatepickerfree', function() {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var language = "es";
            var format = "dd/mm/yyyy";
          //  var f = new Date();
            angular.element(element).datepicker({
                language: language,
                orientation: "top auto",
                autoclose: true,
               // startDate: f,
                format: format,
                todayHighlight: true
            });
        }
    };
});

