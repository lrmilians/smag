/*angular.module('Directives')
    .directive('loadFilename', function($rootScope){
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.bind('change', function(e){
                    if (this.files[0] !== undefined) {
                        var file = this.files[0];
                        /*if(file.type !== 'application/x-pkcs12' ) {
                         alert('Este tipo de archivo no esta soportado actualmente.');
                         this.value = null;
                         return false;
                         }
                        $rootScope.file = file;
                        return true;
                    }
                });
            }
        };
    });*/
