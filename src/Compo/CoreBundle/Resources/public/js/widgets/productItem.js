(function ($) {
    $.widget("compo.productItem", {
        options: {
            //item.id
            //item.name
            //item.price
            //item.quantity = 1
            //item.position = 1
            //item.brand
            //item.category
            //item.list
        },

        _create: function () {
            var self = this;

            self.initHandlers();
        },

        initHandlers: function () {
            var self = this;

            self.initClick();
            self.initBuyClick();
            self.initCompareClick();
        },

        initClick: function () {
            var self = this;

            return;

            self.element.find('a.header, a.img-preview').click(function (e) {
                if (!compo.analytics.adBlockDetected) {
                    e.preventDefault();

                    var url = $(this).attr('href');

                    compo.analytics.reachGoal('compo.products.click', {
                        'eventCategory': 'Ecommerce',
                        'eventAction': 'Click by product',
                        'ecommerce': {
                            'click': {
                                'actionField': {'list': self.options.item.list},
                                'products': [self.options.item]
                            }
                        }
                    }, function () {
                        document.location = url;
                    });
                }
            });
        },

        initBuyClick: function () {
            var self = this;

            $('.buyClick', self.element).click(function () {
                var button = $(this);

                var item = {};

                item.id = self.options.item.id;
                item.name = self.options.item.name;
                item.price = self.options.item.price;
                item.brand = self.options.item.brand;
                item.category = self.options.item.category;
                item.list = self.options.item.list;
                item.position = self.options.item.position;

                item.quantity = 1;

                compo.basket.add(item);

                return false;
            });
        },

        initCompareClick: function () {
            var self = this;

            $('.compareClick', self.element).click(function () {
                var button = $(this);

                var item = {};

                item.id = self.options.item.id;
                item.name = self.options.item.name;
                item.price = self.options.item.price;
                item.brand = self.options.item.brand;
                item.category = self.options.item.category;
                item.list = self.options.item.list;
                item.position = self.options.item.position;

                item.quantity = 1;

                compo.productsCompare.add(item);

                return false;
            });
        }

    });
})(jQuery);