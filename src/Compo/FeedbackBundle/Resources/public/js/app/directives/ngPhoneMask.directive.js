(function () {
    'use strict';

    angular
        .module('Directives')
        .directive('ngPhoneMask', [ngPhoneMask]);

    /* @ngInject */
    function ngPhoneMask() {
        return {
            link: function (scope, elem) {
                elem.mask("+7 (999) 999-99-99");
            }
        }
    }

})();