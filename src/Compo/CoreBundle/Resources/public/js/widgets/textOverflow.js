(function ($) {
    $.widget("compo.textOverflow", {
        _create: function () {
            var self = this;

            self.element.each(function () {
                if ($(this).get(0).scrollWidth > ($(this).outerWidth())) {
                    $(this).tooltip({
                        title: $(this).text(),
                        container: 'body'
                    });

                    $(this).addClass('text-overflow-help');
                }
            });
        }
    });
})(jQuery);