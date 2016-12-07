(function ($) {
    $.widget("compo.menuBrandsList", {

        _create: function () {
            var self = this;

            $('#menu-side-tab-brands-list').each(function () {
                var initLetter = '';

                if ($(this).data('init-letter') != undefined) {
                    initLetter = $(this).data('init-letter');
                }

                if (/[а-я]/i.test(initLetter)) {
                    initLetter = '-';
                }

                $(this).listnav({
                    includeAll: false,
                    prefixes: ['the', 'a'],
                    initLetter: initLetter,
                    noMatchText: 'Нет записей',
                    showCounts: false,
                    includeOther: true
                });

                var rus = '#menu-side-tab-brands-list-nav .-';
                var rus_items = '#menu-side-tab-brands-list .ln--';

                $(rus).html('РУС');
                $(rus).attr('class', 'rus ' + $(rus).attr('class'));

                $(rus_items).addClass('ln-rus');
            });
        }
    });
})(jQuery);