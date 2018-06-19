/*global angular */

/**
 * @type {angular.Module}
 */
(function () {
    'use strict';

    angular
        .module('app.feedback')
        .factory("FeedbackApi", FeedbackApi);

    FeedbackApi.$inject = ['$resource'];

    /* @ngInject */
    function FeedbackApi(resource) {
        var feedback_api = {};

        feedback_api.send = resource(Routing.generate('api_feedback_post'));

        return feedback_api;
    }

})();



