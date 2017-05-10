(function ($) {
    $.widget("compo.captcha", {

        _create: function () {
            var self = this;

            self.element.find('.reloadClick').click(function () {
                self.reload();

                return false;
            });
        },

        reload: function () {
            var self = this;

            self.element.find('img').attr('src', '/captcha/?rnd=' + Math.round(Math.random() * 1000));

            self._trigger("reload", null, {});
        }

    });
})(jQuery);