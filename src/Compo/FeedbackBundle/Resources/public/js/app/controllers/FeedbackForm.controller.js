/*global angular */
/**
 * The main app module
 *
 * @type {angular.Module}
 */
(function () {

    'use strict';

    angular
        .module('app.feedback')
        .controller('FeedbackFormController', FeedbackFormController);

    FeedbackFormController.$inject = ['FeedbackApi'];

    /* @ngInject */
    function FeedbackFormController(FeedbackApi) {

        var vm = this;
        var api = FeedbackApi.send;

        vm.sent = false;
        vm.showerror = false;
        vm.showsuccess = false;
        vm.disablesubmit = false;

        vm.send = send;

        function send() {
            vm.disablesubmit = true;

            api.save({
                data: vm.form
            }, function (response) {

                if (response.success) {
                    vm.sent = true;
                    vm.showsuccess = true;
                } else if (!response.success || response.error) {
                    vm.showerror = true;
                    vm.disablesubmit = false;
                }
            });
        }
    }
})();