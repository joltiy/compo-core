(function ($) {
    $.widget("compo.fixHeightRows", {

        _create: function () {
            var self = this;

            $('.catalog-childs-block .row').each(function () {
                $('.header', this).height(Math.max.apply(Math, $('.header', this).map(function () {
                    return $(this).height();
                }).get()));
            });


            $('.catalog-block-items-list').each(function () {
                var height = Math.max.apply(
                    Math, $('.features', this).map(function () {
                        return $(this).height();
                    }).get()
                );

                if (height > 120) {
                    $('.features', this).css('height', '143px');
                    $('.features', this).css('min-height', '143px');
                } else {
                    $('.features', this).css('height', (height + 6) + 'px');
                    $('.features', this).css('min-height', (height + 6) + 'px');
                }
            });
        }


    });
})(jQuery);