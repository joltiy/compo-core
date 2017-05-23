/*global angular */
/**
 * The main app module
 *
 * @type {angular.Module}
 */
(function () {

    'use strict';

    angular
        .module('app.contacts')
        .controller('FeedbackFormController', FeedbackFormController);
    FeedbackFormController.$inject = ['$http', 'ContactsApi'];
    /* @ngInject */
    function FeedbackFormController(http,ContactsApi) {

        var vm = this,
            api = ContactsApi.send;

        vm.send = send;

        function send () {


            ContactsApi.send.save({
                data: vm
            });

            window.alert('ok');


        }


    }
})();