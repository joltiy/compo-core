(function ($) {
    $.widget("compo.phoneInput", {

        _create: function () {
            var self = this;

            //self.element.mask("+7(999)999-9999");

            //self.element.attr('placeholder', '+7(___)___-____');

            self.element.attr('placeholder', '7XXXXXXXXXX');
            self.element.attr('type', 'tel');
            self.element.attr('autocomplete', 'off');

        }

    });
})(jQuery);