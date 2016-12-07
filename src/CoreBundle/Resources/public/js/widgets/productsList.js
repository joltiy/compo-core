(function ($) {
    $.widget("compo.productsList", {


        _create: function () {
            var self = this;

            var products = [];


            $('.catalog-block-item', self.element).each(function (i, el) {


                el = $(el);

                el.data('position', i);

                var item = {};

                item.id = el.data('id');
                item.name = el.find('.header').text();
                item.price = Number(el.find('.price').eq(0).text().replace(/\D+/g,""));
                item.position = i + 1;
                item.brand = el.find('.manufacture-header').text();
                item.category = el.find('meta[itemprop="category"]').attr('content');
                item.list = el.closest('[itemtype="http://schema.org/ItemList"]').data('list');

                products.push(item);

                $(el).productItem({item: item});

            });

            $(window).trigger("compo.productsList.impressions", [{items:products}]);

        }
    });


})(jQuery);
