(function () {
    'use strict';
    /* @ngInject */
    angular
        .module('app', ['app.ecommerce','app.contacts'])
        .config(['$httpProvider', function($httpProvider) {
            $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        }])
        .filter('unsafe', ['$sce', function($sce){
            return function(text) {
                return $sce.trustAsHtml(text);
            };
        }]);
})();