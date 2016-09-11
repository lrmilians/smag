myEsignature.directive('mydatepicker', function() {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var language = "es";
            var format = "dd/mm/yyyy";
            var f = new Date();
            angular.element(element).datepicker({
                language: language,
                orientation: "top auto",
                autoclose: true,
                startDate: f,
                format: format,
                todayHighlight: true
            });
        }
    };
});

myEsignature.directive('loadFilename', function($translate, dialogs){
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            element.bind('change', function(e){
                if (this.files[0] !== undefined) {
                   /* if(this.files[0].type == "application/pdf" ||
                        this.files[0].type == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
                        this.files[0].type == "application/msword" ||
                        this.files[0].type == "image/jpeg"
                    ){*/
                    this.files[0].name = this.files[0].name.replace(/\s+/g, '_');
                    var ext = this.files[0].name.substr(-4, 4);
                    if(ext == ".p12" || ext == ".pfx"){
                        if(this.files[0].size > 5242880){
                            var fileSize = this.files[0].size;
                            var fileupload = element;
                            fileupload.replaceWith(fileupload = fileupload.val('').clone(true));
                            $translate('msgFileSize').then(function (msg) {
                                dialogs.error('Error', msg + ' ' + parseFloat(fileSize/1048576).toFixed(2) + ' Mb');
                            });
                            return false;
                        } else {
                            var file = this.files[0];
                            scope.file = file;
                            return true;
                        }
                    } else {
                        var fileupload = element;
                        fileupload.replaceWith(fileupload = fileupload.val('').clone(true));
                        $translate('msgFileType').then(function (msg) {
                            dialogs.error('Error', msg);
                        });
                        return false;
                    }
                }
            });
        }
    };
});

myEsignature.directive('loadFilenameXml', function($translate, dialogs){
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            element.bind('change', function(e){
                if (this.files[0] !== undefined) {
                    if(this.files[0].type == "text/xml"){
                        if(this.files[0].size > 5242880){
                            var fileSize = this.files[0].size;
                            var fileupload = element;
                            fileupload.replaceWith(fileupload = fileupload.val('').clone(true));
                            $translate('msgFileSize').then(function (msg) {
                                dialogs.error('Error', msg + ' ' + parseFloat(fileSize/1048576).toFixed(2) + ' Mb');
                            });
                            return false;
                        } else {
                            var file = this.files[0];
                            scope.file = file;
                            return true;
                        }
                    } else {
                        var fileupload = element;
                        fileupload.replaceWith(fileupload = fileupload.val('').clone(true));
                        $translate('msgFileType').then(function (msg) {
                            dialogs.error('Error', msg);
                        });
                        return false;
                    }
                }
            });
        }
    };
});

