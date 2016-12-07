(function ($) {
    $.widget("compo.offer", {

        _create: function () {
            var self = this;

            self.initSostav();

            self.initHandlers();


            $('#sets .show-more, .show-more-line').click(function () {

                var btn = $(this).parent().parent().find('.show-more');

                var parent = btn.parent().parent();
                var rows = parent.find('.more-item');

                $( rows ).toggle( "slow", function() {
                });

                btn.hide();

                parent.find('.hide-more').show();

                parent.find('.show-more-line').hide();


                return false;

            });

            $('#sets .hide-more').click(function () {
                var btn = $(this);
                var parent = btn.parent().parent();
                var rows = parent.find('.more-item');

                $( rows ).toggle( "slow", function() {
                });

                btn.hide();

                parent.find('.show-more').show();

                parent.find('.show-more-line').show();

                return false;
            });
        },

        initHandlers: function () {
            var self = this;

            self.initBuyClick();
            self.initBuyFastClick();
            self.initInvoiceSet();

            self.initCompareClick();
            self.initDetail();

        },

        initDetail: function () {
            var itemEl = $('.product-item');

            if ($('.product-item').length > 0) {
                var item = {};

                item.id = itemEl.data('id');
                item.name = itemEl.find('.header-h1 [itemprop="name"]').text();
                item.price = Number(itemEl.find('[itemprop="price"]').text().replace(/\D+/g,""));
                item.quantity = 1;
                item.position = 1;
                item.brand = itemEl.find('[itemprop="brand"] [itemprop="name"]').text();
                item.category = itemEl.find('meta[itemprop="category"]').attr('content');
                item.list = 'offer';

                $(window).trigger("compo.offer.detail", [{item: item}]);
            }
        },

        initCompareClick: function () {
            $('.product-item .compareClick').click(function () {
                var button = $(this);

                var itemEl = button.closest('[itemtype="http://schema.org/Product"]');

                var item = {};

                item.id = $(this).data('id');
                item.name = itemEl.find('.header-h1 [itemprop="name"]').text();
                item.price = Number(itemEl.find('[itemprop="price"]').text().replace(/\D+/g,""));
                item.quantity = 1;
                item.position = 1;
                item.brand = itemEl.find('[itemprop="brand"] [itemprop="name"]').text();
                item.category = itemEl.find('meta[itemprop="category"]').attr('content');
                item.list = 'offer';

                compo.productsCompare.add(item);

                return false;
            });
        },

        initBuyClick: function () {
            var self = this;

            $('.product-item .buyClick').click(function () {
                var button = $(this);

                var itemEl = button.closest('[itemtype="http://schema.org/Product"]');

                var item = {};

                item.id = button.data('id');
                item.name = itemEl.find('.header-h1 [itemprop="name"]').text();
                item.price = Number(itemEl.find('[itemprop="price"]').text().replace(/\D+/g,""));
                item.brand = itemEl.find('[itemprop="brand"] [itemprop="name"]').text();
                item.category = itemEl.find('meta[itemprop="category"]').attr('content');
                item.list = 'offer';
                item.quantity = 1;
                item.position = 1;


                if ($('#install').length && $('#install').prop('checked')) {
                    item.install = $('#install').val();
                }
                
                compo.basket.add(item);

                return false;
            });
        },

        initBuyFastClick: function () {
            var self = this;

            $('.product-item .buyFastClick').click(function () {
                var button = $(this);

                var itemEl = button.closest('[itemtype="http://schema.org/Product"]');

                var item = {};

                item.id = button.data('id');
                item.name = itemEl.find('.header-h1 [itemprop="name"]').text();
                item.price = Number(itemEl.find('[itemprop="price"]').text().replace(/\D+/g,""));
                item.brand = itemEl.find('[itemprop="brand"] [itemprop="name"]').text();
                item.category = itemEl.find('meta[itemprop="category"]').attr('content');
                item.list = 'offer';
                item.quantity = 1;
                item.position = 1;


                if ($('#install').length && $('#install').prop('checked')) {
                    item.install = $('#install').val();
                }

                compo.basket.addfast(item);

                return false;
            });
        },

        initInvoiceSet: function () {
            var self = this;

            $('.invoiceSet').click(function () {

                var data = self.getComplectsSet();
                
                compo.basket.addSet(data);

                return false;
            });
        },


        getComplectsSet: function () {
            var self = this;

            var product = {};
            var complects = [];

            var type = 'product';
            var complect_type = '';

            if ($('.type-complects2').length > 0 || $('.type-complects').length > 0) {



          
                
                $('#sets .type-complects tr').each(function (i, dom) {


                    if ($('input:checked', dom).length > 0) {
                        type = 'complects';

                        var itemEl = $(dom);

                        if (i > 0) {
                            complect_type = 'complects';
                        }

                        product.id = $('input:checked', itemEl).val();
                        product.name = itemEl.find('.box-complex-f-name-variant').text();
                        product.price = Number(itemEl.find('.box-complex-f-price').eq(0).text().replace(/\D+/g,""));
                        product.brand = itemEl.find('[itemprop="brand"] [itemprop="name"]').text();
                        product.category = itemEl.find('meta[itemprop="category"]').attr('content');
                        product.quantity = 1;
                        product.position = i + 1;
                        product.list = 'offer.complects.variant';
                    }

                    
                    
                });

                $('#sets .type-acomplects tr').each(function (i, dom) {
                    var tr = $(dom);

                    if ($('input', tr).prop('checked')) {
                        type = 'complects';

                        var complect_item = {};

                        complect_item.id = $('input:checked', tr).val();
                        complect_item.name = tr.find('.box-complex-f-name').text();
                        complect_item.price = Number(tr.find('.price').eq(0).text().replace(/\D+/g,""));
                        complect_item.brand = tr.find('[itemprop="brand"] [itemprop="name"]').text();
                        complect_item.category = tr.find('meta[itemprop="category"]').attr('content');
                        complect_item.quantity = 1;
                        complect_item.position = i + 1;
                        complect_item.list = 'offer.complects.item';

                        complects.push(complect_item);
                    }
                });

                $('#sets .type-complects2 tr').each(function (i, dom) {


                    if ($('input:checked', dom).length > 0) {
                        type = 'complects';

                        var itemEl = $(dom);


                        product.id = $('input:checked', itemEl).val();
                        product.name = itemEl.find('.box-complex-f-name-variant').text();
                        product.price = Number(itemEl.find('.box-complex-f-price').eq(0).text().replace(/\D+/g,""));
                        product.brand = itemEl.find('[itemprop="brand"] [itemprop="name"]').text();
                        product.category = itemEl.find('meta[itemprop="category"]').attr('content');
                        product.quantity = 1;
                        product.position = i + 1;
                        product.list = 'offer.complects.variant';
                    }

                });


                $('#sets .type-additionals tr').each(function (i, dom) {
                    var tr = $(dom);

                    if ($('input', tr).prop('checked')) {
                        type = '';

                        var complect_item = {};

                        complect_item.id = $('input:checked', tr).val();
                        complect_item.name = tr.find('.box-complex-f-name').text();
                        complect_item.price = Number(tr.find('.price').eq(0).text().replace(/\D+/g,""));
                        complect_item.brand = tr.find('[itemprop="brand"] [itemprop="name"]').text();
                        complect_item.category = tr.find('meta[itemprop="category"]').attr('content');
                        complect_item.quantity = 1;
                        complect_item.position = i + 1;
                        complect_item.list = 'offer.complects.item';

                        complects.push(complect_item);
                    }
                });

            } else {
                var itemEl = $('.product-item[itemtype="http://schema.org/Product"]');

                product.id = itemEl.data('id');
                product.name = itemEl.find('.header-h1 [itemprop="name"]').text();
                product.price = Number(itemEl.find('[itemprop="price"]').eq(0).text().replace(/\D+/g,""));
                product.brand = itemEl.find('[itemprop="brand"] [itemprop="name"]').text();
                product.category = itemEl.find('meta[itemprop="category"]').attr('content');
                product.quantity = 1;
                product.position = 1;
                product.list = 'offer.complects';

                if ($('#sets .type-acomplects tr').length > 0) {
                    $('#sets .type-acomplects tr').each(function (i, dom) {
                        var tr = $(dom);

                        if ($('input', tr).prop('checked')) {
                            type = 'complects';

                            var complect_item = {};

                            complect_item.id = $('input:checked', tr).val();
                            complect_item.name = tr.find('.box-complex-f-name').text();
                            complect_item.price = Number(tr.find('.price').eq(0).text().replace(/\D+/g,""));
                            complect_item.brand = tr.find('[itemprop="brand"] [itemprop="name"]').text();
                            complect_item.category = tr.find('meta[itemprop="category"]').attr('content');
                            complect_item.quantity = 1;
                            complect_item.position = i + 1;
                            complect_item.list = 'offer.complects.item';

                            complects.push(complect_item);
                        }
                    });
                } else {
                    $('#sets tr').each(function (i, dom) {
                        var tr = $(dom);

                        if ($('input', tr).prop('checked')) {

                            var complect_item = {};

                            complect_item.id = $('input:checked', tr).val();
                            complect_item.name = tr.find('.box-complex-f-name').text();
                            complect_item.price = Number(tr.find('.price').eq(0).text().replace(/\D+/g,""));
                            complect_item.brand = tr.find('[itemprop="brand"] [itemprop="name"]').text();
                            complect_item.category = tr.find('meta[itemprop="category"]').attr('content');
                            complect_item.quantity = 1;
                            complect_item.position = i + 1;
                            complect_item.list = 'offer.complects.item';

                            complects.push(complect_item);
                        }
                    });
                }



            }


            return {item: product, complects: complects, type: type, complect_type: complect_type};
        },

        initSostav: function () {
            //$('#slider-sostav').parent().css('height', $('#slider-sostav').height());

            if ($('#slider-sostav').length) {


                var aside = document.querySelector('#slider-sostav'),
                    opas = $('#slider-sostav').offset(),
                    widthOpas = $('#slider-sostav').width();

                window.onscroll = function () {
                    if (
                        window.pageYOffset >= opas.top
                        &&
                        $(window).width() > 480
                        &&
                        $('#set .slider-item').length > 1
                    ) {
                        aside.className = 'prilip';
                        aside.style.top = 0 + 'px';
                        $('#slider-sostav').width(widthOpas);
                    } else {
                        aside.className = '';
                    }

                }
            }
        },

        changePrice: function (type, price, elem, id) {

            var self = this;

            var itemEl = $(elem).closest('tr');

            var name = itemEl.find('.box-complex-f-name').text();
            var img_href = itemEl.find('.box-complex-f-img').attr('href');

            $('.body-sostav .catalog-block-items-list').productsCatalogBlockSlider("destroy");

            if (type == 'radio') {
                $(".type-complects2 input:checkbox").removeAttr("checked");
                $(".type-complects input:checkbox").removeAttr("checked");

                var main = '<div class="catalog-block-item catalog-block-item-simple"> ' +
                '<span class="img img-preview" style="background-image: url('+img_href+');"></span> ' +
                '<span class="header clamp-2">'+name+'</span> ' +
                '</div>';

                $("#mainset").html(main);
            }

            if (type == 'checkbox') {
                if (elem.checked) {
                    var item_html = '<div class="catalog-block-item catalog-block-item-simple"> ' +
                        '<span class="img img-preview" style="background-image: url('+img_href+');"></span> ' +
                        '<span class="header clamp-2">'+name+'</span> ' +
                        '</div>';

                    $("#set").append("<div class='col-lg-4 slider-item' id='elem" + id + "'>" + item_html + "</div>");
                } else {
                    $("#elem" + id).remove();
                }
            }


            $('.body-sostav .catalog-block-items-list').productsCatalogBlockSlider();


            var result = self.getComplectsSet();

            var sum = parseInt(result.item.price);

            $.each(result.complects, function( index, value ) {

                sum = parseInt(sum) + parseInt(value.price);
            });

            $('#total').text(number_format(sum, 0, '.', ' '));

            $('#countset').text(result.complects.length + 1);

            $('#countitle').text(units(result.complects.length + 1, ['элемент ', 'элемента ', 'элементов']));

            $( window ).scroll();
        }
    });
})(jQuery);