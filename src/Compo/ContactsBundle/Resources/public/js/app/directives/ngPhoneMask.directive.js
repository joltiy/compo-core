(function () {
    'use strict';
    angular
        .module('Directives')
        .directive('ngPhoneMask', ['$location', ngPhoneMask]);
    /* @ngInject */
    function ngPhoneMask($location) {
        return {
            link:
                 function (scope, elem) {
                    elem.mask("+7 (999) 999-99-99");

                }

        }
    }

})();