(function ($) {
    $.widget("compo.gallery", {
        _create: function () {
            var self = this;
            var widget = this;

            $("a[rel=example_group], a[data-fancybox-group]").fancybox({
                'transitionIn': 'elastic',
                'transitionOut': 'elastic',
                'titlePosition': 'outside',
                'titleFormat': function (title, currentArray, currentIndex) {
                    return '<span id="fancybox-title-over">Фото ' + (currentIndex + 1) + ' из ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
                }
            });

            var a = function (self) {
                var fancy = function () {
                    var play = self.options.autoPlay;
                    $('body').find('a.previmages,a.nextimages').remove();
                    self.active.parents('li:first').prevAll().each(function () {
                        var href = $(this).find('img').data('clickThrough');
                        $('body').prepend($('<a>').attr({'href': href, 'class': 'previmages', 'rel': 'gallery'}).css('display', 'none'));
                    });
                    self.active.parents('li:first').nextAll().each(function () {
                        var href = $(this).find('img').data('clickThrough');
                        $('body').append($('<a>').attr({'href': href, 'class': 'nextimages', 'rel': 'gallery'}).css('display', 'none'));
                    });
                    self.anchor.attr('rel', 'gallery');
                    $('a[rel=gallery]').fancybox({
                        onStart: function () {
                            if (play) {
                                self.imgPlay.trigger('click', {'fancybox': 'fancybox'});
                            }
                        }
                    });
                };

                self.anchor.bind('click',{'fancybox': 'fancybox'}, function () {
                    fancy();
                    widget._trigger("show", null, {});
                });

                fancy();
            };

            self.element.PikaChoose({buildFinished: a, transition: [0],   autoPlay: false, fadeThumbsIn: false, showCaption: false, animationSpeed: 100, thumbOpacity: 0.5, showTooltips: false});

            $('.pika-thumbs img').mouseover(function () {
                if (!$(this).parent().parent().hasClass('active')) {
                    $(this).click();
                }
            });
        }
    });
})(jQuery);