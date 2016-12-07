(function ($) {
    $.widget("compo.productsCompare", {

        _create: function () {
            var self = this;

            self.diff();

            if ($('body').hasClass('page-compare')) {
                $('.img-preview').attr('target', '_blank');
                $('.catalog-block-item .header').attr('target', '_blank');
                $('.catalog-block-item .manufacture-header').attr('target', '_blank');
                $('.catalog-block-item .collection-header').attr('target', '_blank');
            }

            $('.compare-type label').click(function () {
                if ($(this).hasClass('diff-all')) {
                    $(this).addClass('active');
                } else {
                    $('.diff-all').removeClass('active');
                }

                self.diff();
            });

            $('.clearCompareClick').click(function () {
                self.clear();

                return false;
            });

            $('.deleteCompareClick').click(function () {
                var item = {};

                item.id = $(this).data('id');

                self.remove(item);

                return false;
            });


            $(window).on("compo.compare.clear", function( event, data ) {
                parent.jQuery.fancybox.close();
            });

            $(window).on("compo.compare.remove", function( event, data ) {
                $('.p' + data.item.id).empty();

                setTimeout(function () {
                    $('.p' + data.item.id).remove();

                    self.diff();
                }, 300);

                if (self.getCount == 0) {
                    parent.jQuery.fancybox.close();
                }
            });


            $(window).on("compo.compare.add", function( event, data ) {
                var body = $('<div id="compareAlert">');

                body.append('<h2>Товар добавлен в список сравнения</h2>');

                var button = $('<button data-href="/compare/" data-fullscreen="1" class="fancybox-iframe btn btn-default pull-right"><span class="glyphicon fa fa-balance-scale"></span> Открыть сравнения</button>');
                button.click(onClickFancyboxFrame);
                button.appendTo(body);

                $.fancybox(
                    body,
                    {
                        'titlePosition': 'inside',
                        'showCloseButton': false,
                        'height': "auto",
                        'autoSize': true,
                        'minHeight': 74,
                        'onComplete': function () {
                            setTimeout(function () {
                                $.fancybox.close();
                            }, 850);
                        }
                    }
                );
            });

        },

        getCount: function () {
            var self = this;

            var data = compo.basket.getData();

            if (data != null && data.compare != undefined && data.compare.count != undefined) {
                return data.compare.count;
            } else {
                compo.basket.load();

                return 0;
            }
        },

        setCount: function (count) {
            var self = this;

            var data = compo.basket.getData();

            data.compare.count = count;

            compo.basket.setData(data);
            parent.compo.basket.setData(data);

            $(window).trigger("compo.compare.update", [{"count": count}]);

            $(parent).trigger("compo.compare.update", [{"count": count}]);


        },

        add: function (item) {
            var self = this;

            var url = '/ajax/compare/';

            $.post(
                url,
                {
                    id: item.id
                },
                function (data) {
                    self.setCount(data.compare.count);

                    $(window).trigger("compo.compare.add", [{item: item}]);

                },
                'json'
            );

            return false;
        },

        remove: function (item) {
            var self = this;

            var url = '/ajax/decompare/';

            $.post(
                url, {
                    id: item.id
                },
                function (data) {
                    self.setCount(data.compare.count);

                    $(window).trigger("compo.compare.remove", [{item: item}]);
                },
                'json'
            );

            return false;
        },

        clear: function () {
            var self = this;

            var url = '/ajax/clearcompares/';

            $.post(
                url,
                {
                    action: 'clear'
                },
                function (data) {
                    self.setCount(0);

                    $(window).trigger("compo.compare.clear", []);
                }
            );
        },

        diff: function () {
            var diff = {};
            var diff_ids = {};

            var features_headers_tr = $('.features-headers tr');
            var list_products_li = $('.list-products table');
            var list_products_tr = $('.list-products tr');

            features_headers_tr.each(function (i, dom) {

                if (i == 0) {
                    return;
                }

                var feature_headers_item = $(this);

                var feature_headers_item_text = feature_headers_item.text().trim();

                if (diff[feature_headers_item_text] == undefined) {
                    diff[feature_headers_item_text] = {};
                }

                diff_ids[feature_headers_item_text] = i;

                list_products_li.each(function () {

                    var product_item = $(this);

                    var feature_item = $('tr', product_item).eq(i);

                    if (feature_item.text().trim() != '') {
                        diff[feature_headers_item_text][feature_item.text().trim()] = feature_item.text().trim();
                    }

                });

                var container = $('.list-products-col');
            });

            features_headers_tr.show();
            list_products_tr.show();

            var notfound = $('.features-headers-notfound');

            if ($('.diff-all').hasClass('active')) {
                features_headers_tr.show();
                list_products_tr.show();
                notfound.hide();
            } else {
                var count = 0;

                for (var header in diff) {
                    if (diff.hasOwnProperty(header)) {
                        var id = diff_ids[header];

                        if (Object.keys(diff[header]).length == 1) {
                            features_headers_tr.eq(id).hide();

                            list_products_li.each(function () {
                                $('tr', this).eq(id).hide();
                            });
                        } else {
                            features_headers_tr.eq(id).show();
                            list_products_li.each(function () {
                                $('tr', this).eq(id).show();
                            });

                            count++;
                        }
                    }
                }

                if (count == 0) {
                    notfound.show();
                } else {
                    notfound.hide();
                }
            }
        }

    });
})(jQuery);