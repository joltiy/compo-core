/*global angular */

/**
 * @type {angular.Module}
 */
(function () {
    'use strict';

    angular
        .module('app.seo')
        .factory("Seo", Seo);

    Seo.$inject = ['$resource'];

    /* @ngInject */
    function Seo($resource) {
        var seo = {
            settings: {
            }
        };


        return seo;
    }
})();
