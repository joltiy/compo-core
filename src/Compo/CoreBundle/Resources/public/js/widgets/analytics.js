(function ($) {
    $.widget("compo.analytics", {
        options: {
            yandexMetrikaId: null,
            userId: null,
            ip: null
        },

        yandexCounterInited: false,
        
        yandexParamsSend: false,
        
        queue: [],

        adBlockDetected: false,

        _create: function () {
            var self = this;


            function adBlockNotDetected() {
                alert('AdBlock is not enabled');
            }

            function adBlockDetected() {
                alert('AdBlock is enabled');
            }

            if(typeof fuckAdBlock === 'undefined') {
                self.adBlockDetected = true;
            } else {
                fuckAdBlock.onDetected(function () {
                    self.adBlockDetected = true;
                });
            }

            fuckAdBlock.setOption('checkOnLoad', false);

            fuckAdBlock.setOption({
                checkOnLoad: true,
                resetOnEnd: false
            });


            self.document.on('yacounter' + self.options.yandexMetrikaId + 'inited', function () {
                self.yandexCounterInited = true;

                self.onGoogleAnalyticsLoaded();
            });
        },





        onGoogleAnalyticsLoaded: function () {
            var self = this;


            if (self.isGoogleAnalyticsLoaded() && self.yandexParamsSend == false) {
                self.yandexParamsSend = true;

                self.window.get(0)['yaCounter' + self.options.yandexMetrikaId].params(self.prepareData());

                $.each(self.queue, function( index, value ) {
                   self.reachGoal(value.event, value.data, value.callback);
                });

                self.queue = [];

            } else {
                setTimeout(function () {
                    if (self.yandexParamsSend == false) {
                        self.onGoogleAnalyticsLoaded();
                    }
                }, 500);
            }
        },








        isGoogleAnalyticsLoaded: function () {
            var self = this;

            return (typeof self.window.get(0).ga === "function" && typeof self.window.get(0).ga.getAll === "function" );
        },



        reachGoal: function(event, data, callback) {
            var self = this;

            if (!(window.yandexMetrikaId || window.google_analytics_id || window.google_tagmanager_id)) {
                if (callback != undefined) {
                    callback();

                }
            }

            if (self.isGoogleAnalyticsLoaded()) {

                data = self.prepareData(data);

                data.event = event;

                data.eventCallback = function () {
                    if (self.yandexCounterInited == false) {
                        self.document.on('yacounter' + self.options.yandexMetrikaId + 'inited', function () {
                            self.window.get(0)['yaCounter' + self.options.yandexMetrikaId].reachGoal(event, data, function () {
                                if (typeof callback === "function") {
                                    callback();
                                }
                            });
                        });
                    } else {
                        self.window.get(0)['yaCounter' + self.options.yandexMetrikaId].reachGoal(event, data, function () {
                            if (typeof callback === "function") {
                                callback();
                            }
                        });
                    }
                };

                if (data.ecommerce != undefined) {
                    var ecommerce = data.ecommerce;



                    var ecomm_pagetype = $('body').data('pagetype');

                    /*
                     home
                        Used on the home page or landing page of your site.
                     searchresults
                        Used on pages where the results of a user's search are displayed.
                     category
                        Used on pages that list multiple items within a category, for example a page showing all shoes in a given style.
                     product
                        Used on individual product pages.
                     cart
                        Used on the cart/basket/checkout page.
                     purchase
                        Used on the page shown once a user has purchased (and so converted), for example a "Thank You" or confirmation page.
                     other
                        Used where the page does not fit into the other types of page, for example a "Contact Us" or "About Us" page.
                     */
                    if (ecomm_pagetype == 'index') {
                        ecomm_pagetype = 'home';
                    } else if (ecomm_pagetype == 'catalog') {
                        ecomm_pagetype = 'category';
                    } else if (ecomm_pagetype == 'category') {
                        ecomm_pagetype = 'category';
                    } else if (ecomm_pagetype == 'search') {
                        ecomm_pagetype = 'searchresults';
                    } else if (ecomm_pagetype == 'cart') {
                        ecomm_pagetype = 'cart';
                    } else if (ecomm_pagetype == 'item') {
                        ecomm_pagetype = 'product';
                    } else {
                        ecomm_pagetype = 'other';
                    }

                    if (ecommerce.purchase != undefined && ecommerce.purchase.products != undefined) {
                        ecomm_pagetype = 'purchase';
                    }


                    var remarketing = {};


                    if (ecommerce.impressions != undefined) {
                        $.each(ecommerce.impressions, function( index, value ) {
                            ecommerce.impressions[index].name = self.translit(ecommerce.impressions[index].name);
                            ecommerce.impressions[index].brand = self.translit(ecommerce.impressions[index].brand);
                            ecommerce.impressions[index].category = self.translit(ecommerce.impressions[index].category);
                        });

                        if (ecomm_pagetype == 'category' || ecomm_pagetype == 'other' || ecomm_pagetype == 'home' || ecomm_pagetype == 'searchresults') {

                            remarketing.ecomm_prodid = [];
                            remarketing.ecomm_totalvalue = 0;

                            if (ecomm_pagetype == 'category') {

                                $.each(ecommerce.impressions, function( index, value ) {
                                    remarketing.ecomm_category = ecommerce.impressions[index].category;
                                });
                            }



                            $.each(ecommerce.impressions, function( index, value ) {
                                remarketing.ecomm_prodid.push(ecommerce.impressions[index].id);
                                remarketing.ecomm_totalvalue = remarketing.ecomm_totalvalue + parseInt(ecommerce.impressions[index].price);
                            });

                            remarketing.ecomm_pagetype = ecomm_pagetype;

                            self.window.get(0).dataLayer.push({
                                'event':'remarketingTriggered',
                                'remarketing': remarketing
                            });
                        }
                    }

                    if (ecommerce.click != undefined && ecommerce.click.products != undefined) {
                        $.each(ecommerce.click.products, function( index, value ) {
                            ecommerce.click.products[index].name = self.translit(ecommerce.click.products[index].name);
                            ecommerce.click.products[index].brand = self.translit(ecommerce.click.products[index].brand);
                            ecommerce.click.products[index].category = self.translit(ecommerce.click.products[index].category);
                        });
                    }

                    if (ecommerce.detail != undefined && ecommerce.detail.products != undefined) {
                        $.each(ecommerce.detail.products, function( index, value ) {
                            ecommerce.detail.products[index].name = self.translit(ecommerce.detail.products[index].name);
                            ecommerce.detail.products[index].brand = self.translit(ecommerce.detail.products[index].brand);
                            ecommerce.detail.products[index].category = self.translit(ecommerce.detail.products[index].category);
                        });

                        $.each(ecommerce.detail.products, function( index, value ) {
                            remarketing.ecomm_category = ecommerce.detail.products[index].category;
                            remarketing.ecomm_totalvalue = parseInt(ecommerce.detail.products[index].price);
                            remarketing.ecomm_prodid = ecommerce.detail.products[index].id;
                        });

                        remarketing.ecomm_pagetype = ecomm_pagetype;

                        self.window.get(0).dataLayer.push({
                            'event':'remarketingTriggered',
                            'remarketing': remarketing
                        });
                    }

                    if (ecommerce.add != undefined && ecommerce.add.products != undefined) {
                        $.each(ecommerce.add.products, function( index, value ) {
                            ecommerce.add.products[index].name = self.translit(ecommerce.add.products[index].name);
                            ecommerce.add.products[index].brand = self.translit(ecommerce.add.products[index].brand);
                            ecommerce.add.products[index].category = self.translit(ecommerce.add.products[index].category);
                        });
                    }

                    if (ecommerce.remove != undefined && ecommerce.remove.products != undefined) {
                        $.each(ecommerce.remove.products, function( index, value ) {
                            ecommerce.remove.products[index].name = self.translit(ecommerce.remove.products[index].name);
                            ecommerce.remove.products[index].brand = self.translit(ecommerce.remove.products[index].brand);
                            ecommerce.remove.products[index].category = self.translit(ecommerce.remove.products[index].category);
                        });
                    }

                    if (ecommerce.checkout != undefined && ecommerce.checkout.products != undefined) {
                        $.each(ecommerce.checkout.products, function( index, value ) {
                            ecommerce.checkout.products[index].name = self.translit(ecommerce.checkout.products[index].name);
                            ecommerce.checkout.products[index].brand = self.translit(ecommerce.checkout.products[index].brand);
                            ecommerce.checkout.products[index].category = self.translit(ecommerce.checkout.products[index].category);
                        });

                        remarketing.ecomm_prodid = [];
                        remarketing.ecomm_totalvalue = 0;

                        $.each(ecommerce.checkout.products, function( index, value ) {
                            remarketing.ecomm_prodid.push(ecommerce.checkout.products[index].id);
                            remarketing.ecomm_totalvalue = remarketing.ecomm_totalvalue + parseInt(ecommerce.checkout.products[index].price);
                        });

                        remarketing.ecomm_pagetype = ecomm_pagetype;

                        self.window.get(0).dataLayer.push({
                            'event':'remarketingTriggered',
                            'remarketing': remarketing
                        });


                    }

                    if (ecommerce.purchase != undefined && ecommerce.purchase.products != undefined) {
                        $.each(ecommerce.purchase.products, function( index, value ) {
                            ecommerce.purchase.products[index].name = self.translit(ecommerce.purchase.products[index].name);
                            ecommerce.purchase.products[index].brand = self.translit(ecommerce.purchase.products[index].brand);
                            ecommerce.purchase.products[index].category = self.translit(ecommerce.purchase.products[index].category);
                        });

                        remarketing.ecomm_prodid = [];
                        remarketing.ecomm_totalvalue = 0;

                        $.each(ecommerce.purchase.products, function( index, value ) {
                            remarketing.ecomm_prodid.push(ecommerce.purchase.products[index].id);
                            remarketing.ecomm_totalvalue = remarketing.ecomm_totalvalue + parseInt(ecommerce.purchase.products[index].price);
                        });

                        remarketing.ecomm_pagetype = ecomm_pagetype;

                        self.window.get(0).dataLayer.push({
                            'event':'remarketingTriggered',
                            'remarketing': remarketing
                        });

                    }

                    if (ecommerce.checkout_option != undefined) {
                        ecommerce.checkout_option.actionField.option = self.translit(ecommerce.checkout_option.actionField.option);
                    }

                    data.ecommerce = ecommerce;
                }

                self.window.get(0).dataLayer.push(data);
            } else {
                self.queue.push({
                    event: event,
                    data: data,
                    callback: callback
                });
            }
        },

        translit: function(text) {
            var transl = new Array();
            transl['А']='A';     transl['а']='a';
            transl['Б']='B';     transl['б']='b';
            transl['В']='V';     transl['в']='v';
            transl['Г']='G';     transl['г']='g';
            transl['Д']='D';     transl['д']='d';
            transl['Е']='E';     transl['е']='e';
            transl['Ё']='Yo';    transl['ё']='yo';
            transl['Ж']='Zh';    transl['ж']='zh';
            transl['З']='Z';     transl['з']='z';
            transl['И']='I';     transl['и']='i';
            transl['Й']='J';     transl['й']='j';
            transl['К']='K';     transl['к']='k';
            transl['Л']='L';     transl['л']='l';
            transl['М']='M';     transl['м']='m';
            transl['Н']='N';     transl['н']='n';
            transl['О']='O';     transl['о']='o';
            transl['П']='P';     transl['п']='p';
            transl['Р']='R';     transl['р']='r';
            transl['С']='S';     transl['с']='s';
            transl['Т']='T';     transl['т']='t';
            transl['У']='U';     transl['у']='u';
            transl['Ф']='F';     transl['ф']='f';
            transl['Х']='X';     transl['х']='x';
            transl['Ц']='C';     transl['ц']='c';
            transl['Ч']='Ch';    transl['ч']='ch';
            transl['Ш']='Sh';    transl['ш']='sh';
            transl['Щ']='Shh';    transl['щ']='shh';
            transl['Ъ']='"';     transl['ъ']='"';
            transl['Ы']='Y';    transl['ы']='y';
            transl['Ь']='';    transl['ь']='';
            transl['Э']='E';    transl['э']='e';
            transl['Ю']='Yu';    transl['ю']='yu';
            transl['Я']='Ya';    transl['я']='ya';

            var result='';
            for(i=0;i<text.length;i++) {
                if(transl[text[i]]!=undefined) { result+=transl[text[i]]; }
                else { result+=text[i]; }
            }

            return result;
        },

        hit: function(url, data) {
            var self = this;

            data = self.prepareData(data);

            if (self.yandexCounterInited == false) {
                self.document.on('yacounter' + self.options.yandexMetrikaId + 'inited', function () {
                    self.window.get(0)['yaCounter' + self.options.yandexMetrikaId].hit(url, {params: data});
                });
            } else {
                self.window.get(0)['yaCounter' + self.options.yandexMetrikaId].hit(url, {params: data});
            }
        },
        
        prepareData: function(data) {
            var self = this;

            if (data == undefined) {
                data = {};
            }

            data.ip = self.options.ip;
            data.clientId = self.getClientId();

            if (self.options.userId != null) {
                data.userId = self.options.userId;
            }

            return data;
        },
        
        getClientId: function () {
            var self = this;

            if (self.isGoogleAnalyticsLoaded()) {
                var tracker = self.window.get(0).ga.getAll()[0];

                return tracker.get('clientId');
            }

        }
        
        
    })
})(jQuery);