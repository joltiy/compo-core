(function ($) {
    $.widget("compo.orderNavigation", {

        defaultElement: $('.line-greyfix'),

        _create: function () {
            var self = this;

            self.refreshVisible();

            self.element.find('.compare .value').text(compo.productsCompare.getCount() + ' шт.');
            self.element.find('.basket .count').text(compo.basket.getCount() + ' шт.');
            self.element.find('.basket .sum').text(number_format(compo.basket.getSum(), 0, '.', ' ') + ' руб.');


            var callback = function (event, data) {

                var text = data.item.name;

                swal(
                    {
                        title: "Товар добавлен в корзину",
                        type: "success",
                        text: text,
                        timer: 2000,
                        showConfirmButton: false,
                        allowOutsideClick: true
                    },
                    function(){

                        $(".sweet-alert").animate(
                            {
                                "top":  $(".line-greyfix .basket .font-icon").offset().top,
                                "left": $(".line-greyfix .basket .font-icon").offset().left * 1.5,

                                "height": $(".line-greyfix .basket .font-icon").height(),
                                "width": $(".line-greyfix .basket .font-icon").width()
                            }, 500, function(){

                                swal.close();

                                $(".sweet-alert").remove();

                                $('.btn-process-order').removeClass('animated');
                                $('.btn-process-order').removeClass('tada');

                                setTimeout(function(){
                                    $('.btn-process-order').addClass('animated tada');
                                }, 100);
                            });
                    }
                );
            };

            $(window).on("compo.basket.add", callback);
            $(window).on("compo.basket.addSet", callback);



            $(window).on("compo.basket.update", function (event, data) {
                self.element.find('.compare .value').text(data.compare.count + ' шт.');

                self.element.find('.basket .count').text(data.stats.products + ' шт.');
                self.element.find('.basket .sum').text(number_format(data.stats.total, 0, '.', ' ') + ' руб.');


                self.refreshVisible();
            });

            $(window).scroll(function () {
                self.refreshVisible();
            });

            self.basketUpdate();

        },

        refreshVisible: function(){
            var self = this;

            var body = $('body');
            var html = $('html');
            var footer = $('#footer');

            if (
                compo.basket.getCount() == 0 && compo.productsCompare.getCount() == 0
            ||
                (body.hasClass('page-compare') || body.hasClass('page-cart') || body.hasClass('page-order'))
            ) {
                self.element.hide();
                $('.line-greyfix .basket').popover('hide');
            } else {
                self.element.show();
            }

            if (
                compo.basket.getCount() == 0
                &&
                !(body.hasClass('page-compare') || body.hasClass('page-cart') || body.hasClass('page-order'))
            ) {
                $('.line-greyfix .basket').popover('hide');
                $('.order-empty').hide();
            } else {

            }

            if ($(window).scrollTop() >= (html.height() - $(window).height() - footer.outerHeight(true))) {
                self.element.css({position: 'absolute', bottom: footer.outerHeight()});
            } else {
                self.element.css({position: 'fixed', bottom: '0'});
            }
        },

        basketUpdate: function(data){
            var self = this;

            var line_greyfix_basket_hover_timeout;

            jQuery.fn.popover.Constructor.prototype.reposition = function () {
                if (this.enabled && this.tip().hasClass("in")) {
                    this.show();
                }
            };

            $('.line-greyfix .basket').popover({
                trigger: "manual" ,
                html: true,
                'container': '.col-popover-basket',
                'viewport': '.col-popover-basket',
                'placement': 'top',
                content: function() {
                    var basket_data = compo.basket.getData();

                    return  $(basket_data.order_html);
                },
                'template': '<div data-toggle="popover" class="popover popover-basket" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
            }).on("mouseenter", function () {
                var _this = this;

                var basket_data = compo.basket.getData();

                if (!basket_data.stats.total) {
                    return;
                }

                if (!$(".popover-basket").length) {
                    $(this).popover("show");
                }

                $(".popover-basket").on("mouseleave", function () {
                    clearTimeout(line_greyfix_basket_hover_timeout);

                    line_greyfix_basket_hover_timeout = setTimeout(function () {
                        if (
                            !$(".popover-basket:hover").length
                            && !$('.line-greyfix .basket:hover').length
                            && !$(".phone-input:hover").length
                            && !$(".phone-input:focus").length
                            && !$(".phone-input:active").length
                        ) {
                            $(_this).popover("hide");
                        }
                    }, 3000);

                });

            }).on("mouseleave", function () {
                var _this = this;

                clearTimeout(line_greyfix_basket_hover_timeout);

                line_greyfix_basket_hover_timeout = setTimeout(function () {
                    if (
                        !$(".popover-basket:hover").length
                        && !$('.line-greyfix .basket:hover').length
                        && !$(".phone-input:hover").length
                        && !$(".phone-input:focus").length
                        && !$(".phone-input:active").length
                    ) {
                        $(_this).popover("hide");
                    }
                }, 3000);
            }).on("click", function (e) {
                var basket_data = compo.basket.getData();

                if (!basket_data.stats.total) {
                    return;
                }

                e.stopPropagation();

                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0 && e.target != 'img.active') {
                    $(_this).popover("hide");
                }

            }).on('shown.bs.popover', function () {
                var _this = this;


                var basket_data = compo.basket.getData();

                if (basket_data.stats.total == 0) {
                    $('.order-empty').show();
                    $('.order-wrap').hide();
                } else {
                    $('.order-empty').hide();
                    $('.order-wrap').show();
                }
/*
                var prevHeight = $('.popover-basket .popover-content').height();

                $(window).on("compo.basket.update", function (event, data) {

                    if (!data.stats.total) {
                        $('.line-greyfix .basket').popover('hide');
                    }

                    if ($('.popover-basket').length && prevHeight != $('.popover-basket .popover-content').height()) {
                        $('.line-greyfix .basket').popover('show');
                    }

                    prevHeight = $('.popover-basket .popover-content').height();
                });
*/
                $('.popover-basket .items-wrap .items').jScrollPane({
                    contentWidth: '0px',
                    autoReinitialise: true
                });

            }).on('inserted.bs.popover', function () {
                var body = $('body');

                if (
                    compo.basket.getCount() == 0 && compo.productsCompare.getCount() == 0
                    ||
                    (body.hasClass('page-compare') || body.hasClass('page-cart') || body.hasClass('page-order'))
                ) {

                } else {
                    compo.order.initHandlers();
                }
            });

            $('.line-greyfix .col-basket').click(function(){
                //$(this).popover("toggle");
            });
        }

    });
})(jQuery);