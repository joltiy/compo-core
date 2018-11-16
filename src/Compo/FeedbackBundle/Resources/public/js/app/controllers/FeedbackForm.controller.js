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

    FeedbackFormController.$inject = ['FeedbackApi', '$analytics'];

    /* @ngInject */
    function FeedbackFormController(FeedbackApi, $analytics) {

        var vm = this;
        var api = FeedbackApi.send;

        vm.sent = false;
        vm.showerror = false;
        vm.showsuccess = false;
        vm.disablesubmit = false;

        vm.send = send;

        function send() {
            vm.disablesubmit = true;

            $analytics.eventTrack(vm.form.type, {
                'category': 'feedback',
                'action': 'send',
                'label': vm.form.type
            });

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
