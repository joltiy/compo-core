(function () {
    'use strict';
    /* @ngInject */
    angular
        .module('app.core', [ 'app.seo', 'app.feedback', 'validation', 'validation.rule'])
        .config(['$validationProvider', function (validationProvider) {
            validationProvider.setExpression({

            });
            validationProvider.setDefaultMsg({
                required: {
                    error: 'Обязательное!',
                    success: 'It\'s Required'
                },
                url: {
                    error: 'Неверный формат URL',
                    success: 'It\'s Url'
                },
                email: {
                    error: 'Неверный формат E-mail',
                    success: 'It\'s Email'
                },
                number: {
                    error: 'Должно быть числом',
                    success: 'It\'s Number'
                },
                minlength: {
                    error: 'Слишком короткое',
                    success: 'Long enough!'
                },
                maxlength: {
                    error: 'Слишком длиное',
                    success: 'Short enough!'
                }
            });
        }])
        .directive("formOnChange", function($parse){
            return {
                require: "form",
                link: function(scope, element, attrs){
                    var cb = $parse(attrs.formOnChange);
                    element.on("change", function(){
                        cb(scope);
                    });
                }
            }
        });

    ;

})();
