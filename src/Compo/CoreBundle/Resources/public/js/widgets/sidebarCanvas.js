(function ($) {
    $.widget("compo.sidebarCanvas", {

        _create: function () {
            var self = this;

            $('[data-toggle=offcanvas]').click(function () {
                $("html, body").animate({scrollTop: 0}, "slow");
                $('.offcanvas').slideToggle(200);

                self._trigger("show", null, {});
            });
        }

    });
})(jQuery);