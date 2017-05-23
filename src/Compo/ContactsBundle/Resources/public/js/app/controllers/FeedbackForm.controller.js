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

        vm.sent = false;
        vm.showerror = false;
        vm.showsuccess = false;
        vm.disablesubmit = false;
        vm.send = send;

        function send () {
            vm.disablesubmit = true;

            api.save({
                data: vm.form
            },function(response){

                if(response.message == 'contacts_sent') {
                    vm.sent = true;
                    vm.showsuccess = true;
                }
                  else if(response.message == 'form_not_valid')
                {

                    vm.showerror = true;
                    vm.disablesubmit = false;
                }

             });




        }


    }
})();