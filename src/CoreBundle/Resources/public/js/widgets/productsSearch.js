(function ($) {
    $.widget("compo.productsSearch", {

        _create: function () {
            var self = this;
            var search = this.element;


            var cache = {};

            search.autocomplete({
                minLength: 2,

                source: function (request, response) {
                    var term = request.term.trim();

                    if (term in cache) {
                        response(cache[term]);
                        return;
                    }

                    request = {'keyword': term, 'simple': 1};


                    $.getJSON(
                        "/search/",
                        request,
                        function (data) {
                            cache[term] = data;
                            response(data);
                        }
                    );
                },
                focus: function () {
                    return false;
                },
                select: function (event, ui) {
                    if (ui.item.all != 1) {
                        window.location.href = '/item/' + ui.item.url;

                    } else {
                        window.location.href = ui.item.url;
                    }

                    return false;
                },
                open: function (event, ui) {
                    event['t'] = 1;

                    var autocomplete = $('ul.search.ui-autocomplete');

                    //autocomplete.offset({ top: autocomplete.position().top, left: autocomplete.position().left -10 });

                    autocomplete.css('width', ( search.width() + 25) + 'px');

                }

            }).autocomplete("instance")._renderItem = function (ul, item) {


                var terms = search.val().trim().split(' ');

                var header = $('<span>').append(item.header);

                terms.forEach(function (term) {
                    header.highlight(term.trim());
                });


                item.header = $(header).html();

                var img = $('<img>').attr('src', '/img/kaplia-index.png');
                var a = $('<a>').attr('href', item.url);

                if (item.all != 1) {
                    img = $('<img>').attr('src', item['picture']);
                    a = $('<a>').attr('href', '/item/' + item.url);
                }

                var title = $('<div>');
                title.addClass('title');
                title.html(item.header);


                var price = $('<div>');
                price.addClass('price');

                if (item.all != 1) {
                    price.html(item['pricecon'] + ' руб.');
                }

                var info = $('<div>');
                info.addClass('wrap');
                info.append(title);
                info.append(price);


                a.append(img);
                a.append(info);


                return $("<li>")
                    .append(a)
                    .appendTo(ul);
            };

            search.autocomplete("instance")._renderMenu = function (ul, items) {
                var that = this;

                $.each(items, function (index, item) {
                    that._renderItemData(ul, item);
                });

                that._renderItemData(ul, {
                    'header': 'Показать все результаты поиска',
                    'url': '/search/?keyword=' + search.val(),
                    'all': 1
                });
            };

            search.autocomplete("widget").addClass("search");
        }

    });
})(jQuery);