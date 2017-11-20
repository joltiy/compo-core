(function () {
    'use strict';
    /* @ngInject */
    angular.module('app.seo', ['yandex-metrika', 'angulartics',  'ngResource'])
        .config(['$metrikaProvider', function ($metrikaProvider) {
            $metrikaProvider.configureCounter({
                id: window.yandexMetrikaId,
                defer: false,
                clickmap: true,
                trackLinks: true,
                accurateTrackBounce: true,
                webvisor: true,
                trackHash: true,
                ecommerce: "dataLayer",
                triggerEvent: true
            });

        }])
        .config(['$analyticsProvider', function ($analyticsProvider) {
            $analyticsProvider.trackExceptions(true);

            $analyticsProvider.firstPageview(true);
            $analyticsProvider.virtualPageviews(false);
            $analyticsProvider.settings.pageTracking.trackRelativePath = false;
            $analyticsProvider.withAutoBase(false);

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

                if (!path) {
                    return;
                }

                var params = {
                    'userId': window.userId
                };

                if (window.yandexMetrikaId != undefined && window.yandexMetrikaId) {

                    if (window['yaCounter' + window.yandexMetrikaId] == undefined) {
                        angular.element(document).on('yacounter' + window.yandexMetrikaId + 'inited', function () {
                            window['yaCounter' + window.yandexMetrikaId].hit(path, {
                                'params': params
                            });
                        });
                    } else {
                        window['yaCounter' + window.yandexMetrikaId].hit(path, {
                            'params': params
                        });
                    }
                }

                var dataLayer = window.dataLayer = window.dataLayer || [];

                dataLayer.push({
                    'event': 'content-view',
                    'content-name': path,
                    'userId': window.userId
                });
            });

            /**
             * Send interactions to the dataLayer, i.e. for event tracking in Google Analytics
             * @name eventTrack
             *
             * @param {string} action Required 'action' (string) associated with the event
             * @param {object} properties Comprised of the mandatory field 'category' (string) and optional  fields 'label' (string), 'value' (integer) and 'noninteraction' (boolean)
             */

            $analyticsProvider.registerEventTrack(function(event, properties){
                var dataLayer = window.dataLayer = window.dataLayer || [];


                properties = properties || {};

                var data = {
                    'event': event,

                    'target': properties.category,
                    'action': properties.action,
                    'target-properties': properties.label,
                    'value': properties.value,

                    'interaction-type': properties.noninteraction,
                    'ecommerce': properties.ecommerce,
                    'userId': window.userId
                };

                dataLayer.push(data);

                if (window.yandexMetrikaId != undefined && window.yandexMetrikaId) {

                    if (window['yaCounter' + window.yandexMetrikaId] == undefined) {
                        angular.element(document).on('yacounter' + window.yandexMetrikaId + 'inited', function () {
                            window['yaCounter' + window.yandexMetrikaId].reachGoal(event, data);

                        });
                    } else {
                        window['yaCounter' + window.yandexMetrikaId].reachGoal(event, data);
                    }
                }


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
        .run(['$analytics', function($analytics){
            angular.element(document).on('yacounter' + window.yandexMetrikaId + 'inited', function () {
                if (window.userId != undefined && window.userId) {
                    window['yaCounter' + window.yandexMetrikaId].setUserID(window.userId);
                }
            });
        }])
    ;
})();
