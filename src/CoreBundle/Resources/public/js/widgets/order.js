(function ($) {
    $.widget("compo.order", {

        _create: function () {
            var self = this;

            self.initHandlers();

            $("#km").inputNumber();

            $('.additional-phone-create').click(function(){
                if ($('.additional-phones').is(':visible') == false) {
                    $('.additional-phones').show();
                    return;
                }

                var template = $('.additional-phone-template').clone();

                template.removeClass('additional-phone-template');
                $('input', template).val('');

                $('.additional-phones').append(template);

                $('.remove', template).click(function(){
                    $(this).closest('.additional-phone-item').remove();

                    self.onChangeAdditionalPhones();
                });

                $('input', template).change(function(){
                    self.onChangeAdditionalPhones();
                });

                $(".phone-input", template).phoneInput();


                self.onChangeAdditionalPhones();
            });

            var phones = $.cookie('additional_phone');

            if (phones == undefined) {
                phones = [''];
            } else {
                phones = phones.split(',');
            }

            $.each(phones, function( index, value ) {
                if (value.trim() == '') {
                    return;
                }

                $('.additional-phones').show();

                if (index == 0) {
                    $('.additional-phone-item input').eq(0).val(value);

                    $('.additional-phone-item input').eq(0).change(function(){
                        self.onChangeAdditionalPhones();
                    });

                } else {
                    var template = $('.additional-phone-template').clone();
                    $(".phone-input", template).phoneInput();

                    template.removeClass('additional-phone-template');
                    $('input', template).val(value);

                    $('input', template).change(function(){
                        self.onChangeAdditionalPhones();
                    });
                    $('.additional-phones').append(template);

                    $('.remove', template).click(function(){
                        $(this).closest('.additional-phone-item').remove();

                        self.onChangeAdditionalPhones();
                    });
                }
            });

            self.onChangeAdditionalPhones();


            $(window).on("compo.basket.update", function (event, data) {

                if (data.quantity == 0) {
                    $('.order-empty').show();
                    $('.order-wrap').hide();
                } else {
                    $('.order-empty').hide();
                    $('.order-wrap').show();
                }


                $('#top_basket_products').html(data.quantity);


                $('.order-foot').replaceWith(data.order_foot_html);

                $('.delivery-price-value').html(data.delivery_cost);

                $('.payment-description').hide();

                $('#payment_description_id_' + data.payment_id).show();

                $('.delivery-description').hide();

                $('#delivery_description_id_' + data.delivery_id).show();





                self.updateBasketIntems(data);
            });

        },

        onChangeAdditionalPhones: function () {
            var phones = [];

            $('.additional-phone-item input').each(function(index,item){

                if ($(item).val().trim() != '') {
                    phones.push($(item).val());

                }
            });

            phones = $.grep(phones, function(el, index) {
                if (el.toString() == '') {
                    return true;
                }
                return index === $.inArray(el, phones);
            });

            $.cookie('additional_phone', phones.join(','), { expires: 36000, path: '/', secure: false, domain: window.location.host });
        },

        updateBasketIntems: function (data) {
            var self = this;

            var products = {};

            if (data.quantity == 0) {
                $('.order-empty').show();
                $('.order-wrap').hide();
            } else {
                $('.order-empty').hide();
                $('.order-wrap').show();
            }

            $.each( data.contents, function( key, value ) {
                products[value.id] = value;
            });

            $('.product-row').each(function(){
                var product_id = $(this).data('id');

                if (products[product_id] != undefined) {
                    $('#product' + product_id).val(products[product_id].quantity);
                    $('#total_' + product_id).html('<span>' + number_format(products[product_id].total, 0, ',', ' ') + ' р.</span>');

                } else {
                    $('#product_row_' + product_id).remove();
                }
            });

            $.each( data.contents, function( key, value ) {
                var product_id = value.id;

                if ($('#product' + product_id).length > 0) {

                } else {
                    $('#cmpobasket .items').append(value.html);

                    self.initHandlersRow($('#product_row_'+product_id));
                }
            });
        },

        initHandlersRow: function (row) {
            var self = this;

            $('.deleteProduct', row).each(function(){
                $(this).click(function(){

                    var item = {
                        id: $(this).data('id'),
                        type: $(this).data('type')
                    };

                    compo.basket.remove({item: item});

                    return false;
                });
            });



            $('.count input', row).change(function(){
                var el = this;

                var item = {
                    id: $(el).data('id'),
                    type: $(el).data('type'),
                    count: $(el).val()
                };


                compo.basket.changeQuantity({item: item});
            });


            $('.input-number', row).inputNumber();
        },

        initHandlers: function () {
            var self = this;

            self.initSetDeliverySelect();

            self.initSetPaymentSelect();

            $('.terms-checkbox').change(function(){
                if($(this).prop('checked')) {
                    $(this).closest('.form-group').removeClass('has-error');
                    $('.btn-order').removeAttr('disabled');
                } else {
                    $(this).closest('.form-group').addClass('has-error');
                    $('.btn-order').attr('disabled', 'disabled');
                }
            });

            var basket_data = compo.basket.getData();

            if (basket_data != undefined && basket_data.quantity == 0) {
                $('.order-empty').show();
                $('.order-wrap').hide();
            } else {
                $('.order-empty').hide();
                $('.order-wrap').show();
            }

            $('#cmpobasket .product-row').each(function(){
                self.initHandlersRow($(this));
            });

            var orderform = $("#orderform");

            orderform.garlic();

            orderform.validate({
                rules: {
                    phone: {
                        required: true,
                        minlength: 11
                    }
                }
            });
            
            $(orderform).submit(function(event){
                if (!orderform.valid()) {
                    return false;
                }
                $(this).find('.btn-order').button('loading');
            });

            $(".phone-input").phoneInput();

            var orderform_quick = $("#orderform_quick");

            orderform_quick.garlic();

            orderform_quick.validate({
                rules: {
                    phone: {
                        required: true,
                        minlength: 11
                    }
                }
            });

            $(".phone-input", orderform_quick).phoneInput();


            $(orderform_quick).submit(function(event){
                

                if (!orderform_quick.valid()) {
                    return false;
                }

                event.preventDefault();

                var data = {};

                $.each($( '#orderform' ).serializeArray(), function(_, kv) {
                    data[kv.name] = kv.value;
                });

                data.phone = $('#phone2', orderform_quick).val();

                data.is_quick = 1;


                $(this).find('.btn').button('loading');
// compo_order_create
                $.ajax({
                    type: "POST",
                    url: Routing.generate('compo_order_create'),
                    data: data,
                    success: function(response){
                        swal(
                            {
                                title: response.title,
                                type: "success",
                                text: response.html,
                                html: true,
                                allowOutsideClick: true
                            }
                        );

                        //compo.basket.setData(response);

                        response.is_quick = 1;

                        self.purchase(response);
                    },
                    dataType: 'json'
                });
            });
        },

        initSetPaymentSelect: function () {
            var self = this;

            $('#payment-select').change(function(){
                var pid = $('option:selected', $(this)).val();
                var title = $('option:selected', $(this)).text().trim();

                $.post(
                    Routing.generate('compo_basket_setpayment'),
                    {
                        id: pid
                    },
                    function (data) {
                        compo.basket.setData(data);

                        $(window).trigger("compo.order.setPayment", [data]);

                        $(window).trigger("compo.order.checkoutOption", {
                            'ecommerce': {
                                'checkout_option': {
                                    'actionField': {'step': 2, 'option': title}
                                }
                            }
                        });

                    }, 'json'
                );
            });
        },

        initSetDeliverySelect: function () {
            var self = this;


            $('#delivery-select').change(function(){

                var pid = $('option:selected', $(this)).val();
                var title = $('option:selected', $(this)).text().trim();

                $.post(
                    Routing.generate('compo_basket_setdelivery'),
                    {
                        id: pid
                    },
                    function (data) {
                        compo.basket.setData(data);

                        $(window).trigger("compo.order.setDelivery", [data]);


                        $(window).trigger("compo.order.checkoutOption", {
                            'ecommerce': {
                                'checkout_option': {
                                    'actionField': {'step': 1, 'option': title}
                                }
                            }
                        });

                    }, 'json'
                );
            });

        },

        setKilometers: function () {
            var self = this;

            var km = $('#km').val() * 1;

            $.post(
                "/ajax/setkilometers/",
                {
                    kilometers: km
                },
                function (data) {
                    compo.basket.setData(data);

                    $(window).trigger("compo.order.setKilometers", [data]);

                }, 'json'
            );
        },


        purchase: function (data) {
            var self = this;

            $(window).trigger("compo.order.purchase", [data]);

            compo.basket.load();
        },

        checkout: function (data) {
            var self = this;

            $(window).trigger("compo.order.checkout", [data]);
        }


    });
})(jQuery);