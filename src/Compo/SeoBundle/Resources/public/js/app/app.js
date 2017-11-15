(function () {
    'use strict';
    /* @ngInject */
    angular.module('app.seo', ['angulartics', 'yandex-metrika', 'ngResource'])
        .config(['$metrikaProvider', function ($metrikaProvider) {
            $metrikaProvider.configureCounter({
                id: window.yandexMetrikaId,
                clickmap: true,
                trackLinks: true,
                accurateTrackBounce: true,
                webvisor: true,
                trackHash: true,
                ecommerce: "dataLayer"
            });

        }])
        .config(['$analyticsProvider', function ($analyticsProvider) {
            $analyticsProvider.trackExceptions(true);

            if (window.userId != undefined) {
                $analyticsProvider.settings.ga = {
                    userId: window.userId
                };
            }


            /**
             * Send content views to the dataLayer
             *
             * @param {string} path Required 'content name' (string) describes the content loaded
             */

            $analyticsProvider.registerPageTrack(function(path){

                var dataLayer = window.dataLayer = window.dataLayer || [];

                dataLayer.push({
                    'event': 'content-view',
                    'content-name': path
                });
            });

            /**
             * Send interactions to the dataLayer, i.e. for event tracking in Google Analytics
             * @name eventTrack
             *
             * @param {string} action Required 'action' (string) associated with the event
             * @param {object} properties Comprised of the mandatory field 'category' (string) and optional  fields 'label' (string), 'value' (integer) and 'noninteraction' (boolean)
             */

            $analyticsProvider.registerEventTrack(function(action, properties){
                var dataLayer = window.dataLayer = window.dataLayer || [];
                properties = properties || {};

                dataLayer.push({
                    'event': properties.event || 'interaction',
                    'target': properties.category,
                    'action': action,
                    'target-properties': properties.label,
                    'value': properties.value,
                    'interaction-type': properties.noninteraction,
                    'ecommerce': properties.ecommerce
                });

            });

            $analyticsProvider.registerSetUsername(
                /**
                 * Send user's data to the datalayer, i.e. for user tracking in Google Analytics
                 * @param  {string} username   login of the username
                 * @param  {object} properties List of attribute of the current username
                 * @return {void}
                 */
                function (username, properties) {
                    var dataLayer = window.dataLayer = window.dataLayer || [];
                    properties = properties || {};
                    dataLayer.push({
                        'username': username,
                        'user': properties
                    });
                }
            );

        }])
    ;
})();
