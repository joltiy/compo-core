(function () {
    'use strict';
    /* @ngInject */
    angular.module('app.feedback', ['ngResource', 'Directives', 'validation', 'validation.rule'])
    .config(['$qProvider', function ($qProvider) {
        $qProvider.errorOnUnhandledRejections(false);
    }]);
})();
