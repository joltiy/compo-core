(function ($) {
    $.widget("compo.popoverFix", {

        _create: function () {
            var self = this;

            $('body').on('click', function (e) {
                $('[data-toggle="popover"]').each(function () {
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0 && !$(e.target).hasClass('active')) {
                        $(this).popover('hide');
                    }
                });
            });

            $('[data-toggle="popover"]').popover({trigger: 'manual', container: "body", html: true})
                .click(function (e) {
                    $(this).popover('toggle');
                    e.stopPropagation();
                });
        }

    });
})(jQuery);