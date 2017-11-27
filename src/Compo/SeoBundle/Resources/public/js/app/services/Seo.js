/*global angular */

/**
 * @type {angular.Module}
 */
(function () {
    'use strict';

    angular
        .module('app.seo')
        .factory("Seo", Seo)
    ;

    Seo.$inject = ['$resource', '$analytics', '$analyticsProvider', '$metrika'];

    /* @ngInject */
    function Seo($resource, $analytics, $analyticsProvider, $metrika) {
        var seo = {
            settings: {
            }
        };



        return seo;
    }
})();
