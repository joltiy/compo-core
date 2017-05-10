(function ($) {
    $.widget("compo.productsCatalogBlockSlider", {

        _create: function () {
            var self = this;


            if ($('body').hasClass('page-compare')) {
                self.element.slick({
                    arrows: true,
                    infinite: false
                });
            } else {
                self.element.slick({
                    arrows: true,
                    infinite: false,
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 769,
                            settings: {
                                slidesToScroll: 1,

                                slidesToShow: 1
                            }
                        },
                        {
                            breakpoint: 1025,
                            settings: {
                                slidesToScroll: 1,

                                slidesToShow: 2
                            }
                        },
                        {
                            breakpoint: 1280,
                            settings: {
                                slidesToScroll: 1,

                                slidesToShow: 3
                            }
                        }
                    ]
                });

            }

            self.element.on('swipe', function(event, slick, direction){
                var list = self.element.closest('[itemtype="http://schema.org/ItemList"]').data('list');

                self._trigger("swipe", null, {
                    list: list
                });
            });

            self.element.on('beforeChange', function(event, slick, direction){
                var list = self.element.closest('[itemtype="http://schema.org/ItemList"]').data('list');

                self._trigger("beforeChange", null, {
                    list: list
                });
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                self.element.slick('setPosition');
            });

        },

        _destroy: function() {
            this.element.unbind( "beforeChange" );
            this.element.unbind( "swipe" );

            this.element.slick( "destroy" );
        }

    });
})(jQuery);