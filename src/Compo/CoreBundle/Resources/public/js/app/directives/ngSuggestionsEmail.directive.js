(function () {
    'use strict';

    angular
        .module('Directives2')
        .directive('ngSuggestionsEmail', [ngSuggestionsEmail]);

    /* @ngInject */
    function ngSuggestionsEmail() {

        return {
            link: function (scope, elem) {
                setTimeout(function () {
                    elem.suggestions({
                        token: "ea23c03a62073c311b71bec1553b92d6b40c67b6",
                        type: "EMAIL",
                        count: 5,
                        onSelect: function (suggestion) {
                        }
                    });
                }, 500);

            }
        }
    }

})();
