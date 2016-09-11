myAdmin.directive('myStrengthpass', function() {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            angular.element(element).strength({
                strengthClass: 'strength',
                strengthMeterClass: 'strength_meter',
                strengthButtonClass: 'button_strength',
                idElement: 'password_input'
            });
        }
    };
});




