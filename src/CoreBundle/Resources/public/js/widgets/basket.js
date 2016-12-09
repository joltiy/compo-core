(function ($) {
    $.widget("compo.basket", {
        
        is_process_load_data: false,

        is_local_storage_enabled: false,

        _create: function () {
            var self = this;

            try {
                $.jStorage.set('is_local_storage_enabled', 1);

                $.jStorage.get('is_local_storage_enabled');

                self.is_local_storage_enabled = true;

            } catch (e) {
                self.is_local_storage_enabled = false;
            }


            //$.jStorage.setTTL('basket_data', 1000);

            var basket_data = self.getData();

            if (window.location.protocol == 'http:') {
                //$('body').append('<iframe style="display:none" name="storage" src="https://' + window.location.host + '/assets/compo/storage.html"></iframe>');
            }

            if (true) {

            //if (basket_data == undefined || basket_data == null) {
                self.load();
            } else {
                $(window).trigger("compo.basket.update", [basket_data]);

                self.listenKeyChange();
            }


            self.listenCookieChange();

            self.updateData();

        },

        updateData: function () {
            var self = this;

            if (!self.is_local_storage_enabled) {
                self.basket_data_update_date = $.cookie('basket_data_update_date');

                setInterval(function() {

                    if ($.cookie('basket_data_update_date') !== self.basket_data_update_date) {
                        self.load();
                        self.basket_data_update_date = $.cookie('basket_data_update_date');
                    }
                }, 1000);
            }
        },



        listenKeyChange: function () {
            var self = this;

            if (self.is_local_storage_enabled) {
                $.jStorage.listenKeyChange("basket_data", function(key, action){
                    var basket_data = self.getData();

                    $(window).trigger("compo.basket.update", [basket_data]);
                });
            } else {

            }

        },

        listenCookieChange: function () {
            var self = this;

            setInterval(function() {
                if ($.cookie('basket_data_update') == 1) {
                    if (window.location.protocol == 'http:' && $.cookie('basket_data_update_http') == 0) {
                        $.cookie('basket_data_update', 0, { expires: 36000, path: '/', secure: false, domain: window.location.host });

                        self.load();
                    }

                    if (window.location.protocol == 'https:' && $.cookie('basket_data_update_http') == 1) {
                        $.cookie('basket_data_update', 0, { expires: 36000, path: '/', secure: false, domain: window.location.host });

                        self.load();
                    }
                }
            }, 1000);
        },

        getData: function () {
            var self = this;

            if (self.is_local_storage_enabled) {
                var data = $.jStorage.get('basket_data');
            } else {
                var data = self.data;
            }

            if ($('#delivery-select option[value="' + 1 + '"]').length > 0) {


                if (data != undefined && data != null && data.delivery_id != undefined && data.delivery_id != null && $('#delivery-select option:selected').val() != data.delivery_id) {
                    $('#delivery-select option[value="' + data.delivery_id + '"]').prop("selected", true);

                    $('#delivery-select').change();
                }

                if (data != undefined && data != null && data.delivery_min != undefined && data.delivery_min != null && parseInt(data.delivery_min) >= 800) {
                    $('#delivery-select option[value="' + 1 + '"]').hide();
                    $('#delivery-select option[value="' + 2 + '"]').hide();
                } else {
                    $('#delivery-select option[value="' + 1 + '"]').show();
                    $('#delivery-select option[value="' + 2 + '"]').show();
                }
            }


            return data;
        },

        setData: function (data) {
            var self = this;


            if (data != undefined && data != null && data.delivery_id != undefined && data.delivery_id != null && $('#delivery-select option:selected').val() != data.delivery_id) {
                $('#delivery-select option[value="' + data.delivery_id + '"]').prop("selected", true);

                $('#delivery-select').change();
            }



            if (self.is_local_storage_enabled) {
                $.jStorage.set('basket_data', data);
            } else {
                var d = new Date();
                var d_string = d.toString();

                $.cookie('basket_data_update_date', d_string, { expires: 36000, path: '/', secure: false, domain: window.location.host });

                self.basket_data_update_date = d_string;
                self.data = data;
            }

            $(window).trigger("compo.basket.update", [data]);

            if (window.location.protocol == 'http:') {
                $.cookie('basket_data_update_http', 1, { expires: 36000, path: '/', secure: false, domain: window.location.host });
            } else {
                $.cookie('basket_data_update_http', 0, { expires: 36000, path: '/', secure: false, domain: window.location.host });
            }

            $.cookie('basket_data_update', 1, { expires: 36000, path: '/', secure: false, domain: window.location.host });

            if (data != undefined && data != null && data.delivery_min != undefined && data.delivery_min != null && parseInt(data.delivery_min) >= 800) {
                $('#delivery-select option[value="' + 1 + '"]').hide();
                $('#delivery-select option[value="' + 2 + '"]').hide();
            } else {
                $('#delivery-select option[value="' + 1 + '"]').show();
                $('#delivery-select option[value="' + 2 + '"]').show();
            }
        },

        load: function () {
            var self = this;

            if (self.is_process_load_data) {
                return;
            }
            self.is_process_load_data = true;


            $.get(
                Routing.generate('compo_basket_data'),
                {
                },
                function (data) {

                    if (self.is_local_storage_enabled) {
                        $.jStorage.stopListening('basket_data');

                        $.jStorage.set('basket_data', data);
                    } else {
                        self.data = data;

                    }


                    self.is_process_load_data = false;

                    if (self.is_local_storage_enabled) {
                        self.listenKeyChange();
                    }


                    $(window).trigger("compo.basket.update", [data]);
                },
                'json'
            );
        },

        getCount: function () {
            var self = this;

            var data = self.getData();

            if (data != null && data.stats != undefined && data.stats.products != undefined) {
                return data.stats.products;
            } else {
                compo.basket.load();

                return 0;
            }
        },

        getSum: function () {
            var self = this;

            var data = self.getData();

            if (data != null && data.stats != undefined && data.stats.total != undefined) {
                return data.stats.total;
            } else {
                compo.basket.load();

                return 0;
            }
        },

        addSet: function (data) {
            var self = this;
            
 

            var invoice = {
                'product': data.item.id,
                'products': []
            };

            var url = '/ajax/addset/';

            if (data.type == 'complects') {
                url = '/ajax/addsetold/';
            }
            
            if (data.complect_type == 'complects') {
                invoice.complect = data.item.id;
            }
            
            $.each(data.complects, function( index, value ) {
                invoice.products.push(value.id);
            });

            $.post(
                url,
                invoice,
                function (response) {
                    self.setData(response);

                    $(window).trigger("compo.basket.addSet", [data]);
                },
                'json'
            );
        },

        add: function (data) {
            var self = this;

            var url = Routing.generate('compo_basket_add_product');

            $.post(
                url,
                {
                    id: data.id,
                    qnt: data.quantity,
                    install: data.install
                },
                function (response) {
                    self.setData(response);

                    $(window).trigger("compo.basket.add", [{item: data}]);
                },
                'json'
            );

            return false;
        },

        addfast: function (data) {
            var self = this;

            var url = '/ajax/addproduct/';

            $.post(
                url,
                {
                    id: data.id,
                    qnt: data.quantity,
                    install: data.install
                },
                function (response) {
                    self.setData(response);
                    //$(window).trigger("compo.basket.add", [{item: data}]);
                    document.location.href='/cart/';
                },
                'json'
            );

            return false;
        },

        remove: function (data) {
            var self = this;

            $.post(
                Routing.generate('compo_basket_delete_product'),
                {
                    id: data.item.id,
                    type: data.item.type
                },
                function (response) {
                    $(window).trigger("compo.basket.remove", [data]);

                    compo.basket.setData(response);
                }, 'json'
            );
        },

        changeQuantity: function (data) {
            var self = this;

            var url = Routing.generate('compo_basket_recount_product');

            $.get(
                url,
                {
                    id: data.item.id,
                    type: data.item.type,
                    count: data.item.count
                },
                function (response) {
                    $(window).trigger("compo.basket.changeQuantity", [data]);

                    compo.basket.setData(response);
                }, 'json'
            );
        }
    });
})(jQuery);