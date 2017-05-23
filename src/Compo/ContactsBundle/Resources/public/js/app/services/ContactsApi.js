/*global angular */

/**
 * @type {angular.Module}
 */
(function() {
    'use strict';

    angular
        .module('app.contacts')
        .factory("ContactsApi", ContactsApi);

    ContactsApi.$inject = ['$resource'];

    /* @ngInject */
    function ContactsApi(resource)
    {
        var contacts_api = {

        };

        contacts_api.send = resource(Routing.generate('api_contacts_post_dispatch'));



        return contacts_api;
    }

})();



