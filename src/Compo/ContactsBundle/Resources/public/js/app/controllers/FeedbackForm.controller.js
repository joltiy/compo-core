/*global angular */
/**
 * The main app module
 *
 * @type {angular.Module}
 */
(function () {

    'use strict';

    angular
        .module('app')
        .controller('FeedbackFormController', FeedbackFormController);
    FeedbackFormController.$inject = ['$http', '$compile'];
    /* @ngInject */
    function FeedbackFormController(http,compile) {

        var v = this;
        var base_url = Routing.generate('compo_contacts_api');


        v.data = {};
        v.send = send;

        function send () {


            http({
                'url': base_url,
                'method': 'POST',
                'headers': { Accept: 'application/json' },
                'data': v
            }).then(function(response) {
                window.alert(response);
            });




        }


    }
})();