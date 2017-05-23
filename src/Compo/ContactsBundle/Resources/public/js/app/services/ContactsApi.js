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

        contacts_api.send = resource(
            Routing.generate('post_contacts_dispatch', {'_format': 'json', 'data': ':data'}).replace(/%3A/, ':'),
            {
                data: "@data"
            }
        );



        return contacts_api;
    }

})();



