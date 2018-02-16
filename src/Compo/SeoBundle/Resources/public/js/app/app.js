(function () {
    'use strict';
    /* @ngInject */
    angular.module('app.seo', ['yandex-metrika', 'angulartics',  'ngResource'])
        .config (['$locationProvider', function($locationProvider) {
            $locationProvider.html5Mode({
                enabled : true,
                requireBase: false,
                rewriteLinks : false
            });
        }])
        .config(['$metrikaProvider', function ($metrikaProvider) {
            $metrikaProvider.configureCounter({
                id: window.yandexMetrikaId,
                defer: true,
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

            var getClientsId = function() {
                var clients_id = {
                    google_analytics_client_id: null,
                    yandex_metrika_client_id: null,
                };

                var match = document.cookie.match('(?:^|;)\\s*_ga=([^;]*)');

                var raw = (match) ? decodeURIComponent(match[1]) : null;

                if (raw) {
                    match = raw.match(/(\d+\.\d+)$/);
                }

                var gacid = (match) ? match[1] : null;

                if (gacid) {
                    clients_id.google_analytics_client_id = gacid;
                }

                var match = document.cookie.match('(?:^|;)\\s*_ym_uid=([^;]*)');

                var raw = (match) ? decodeURIComponent(match[1]) : null;

                if (raw) {
                    clients_id.yandex_metrika_client_id = gacid;
                }

                return clients_id;
            };


            /**
             * Send content views to the dataLayer
             *
             * @param {string} path Required 'content name' (string) describes the content loaded
             */

            $analyticsProvider.registerPageTrack(function(path){

                if (!path) {
                    return;
                }

                var clients_id = getClientsId();

                var params = {
                    'userId': window.userId,
                    'google_analytics_client_id': clients_id.google_analytics_client_id,
                    'yandex_metrika_client_id': clients_id.yandex_metrika_client_id
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
                    'userId': window.userId,
                    'params': params
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

                var clients_id = getClientsId();

                var data = {
                    'event': event,

                    'target': properties.category,
                    'action': properties.action,
                    'target-properties': properties.label,
                    'value': properties.value,

                    'interaction-type': properties.noninteraction,
                    'ecommerce': properties.ecommerce,
                    'userId': window.userId,
                    'google_analytics_client_id': clients_id.google_analytics_client_id,
                    'yandex_metrika_client_id': clients_id.yandex_metrika_client_id
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
