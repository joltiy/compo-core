(function ($) {
    $.widget("compo.backToTop", {

        defaultElement: $('<a href="#container" id="back-to-top"><i class="fa fa-chevron-up"></i></a>'),

        options: {
            appendTo: $('body'),
            wrapper: $('#container'),
            size: 30,
            bottom: 60,
            correction: 0,
            padding: 15,
            zIndex: 8000,
            viewport: 1500
        },

        _create: function () {
            var self = this;

            self.element.appendTo(self.options.appendTo);

            self.initStyle();

            self.initSmoothScroll();

            self.calcViewport();

            self.bindHandlers();

            self.refreshPosition();
            self.refreshVisible();
        },

        initSmoothScroll: function () {
            var self = this;

            self.element.smoothScroll({ offset: -self.options.padding });
        },

        initStyle: function () {
            var self = this;

            self.element.css('z-index', self.options.zIndex);
        },

        bindHandlers: function () {
            var self = this;

            self.window.resize(function () {
                self.refreshPosition();
            });

            self.window.scroll(function () {
                self.refreshVisible();
            });

            self.element.click(function () {
                self._trigger("click", null, {});
            });
        },

        calcViewport: function () {
            var self = this;

            if ($('body').hasClass('page-compare')) {
                self.options.correction = self.options.size + self.options.padding;
            } else {
                self.options.correction = self.options.size + self.options.padding;
            }

            self.options.viewport = self.options.viewport + (self.options.correction + self.options.padding * 2);
        },

        refreshPosition: function () {
            var self = this;

            if (self.window.width() < self.options.viewport) {
                self.element.css('right', (((parseInt(self.options.wrapper.css('margin-right')) - self.options.correction)  ) + self.options.correction + self.options.padding) + 'px');
                self.element.css('bottom', (parseInt(self.options.size * 2)) + 'px');
            } else {
                self.element.css('right', (parseInt(self.options.wrapper.css('margin-right')) - self.options.correction) + 'px');
                self.element.css('bottom', (parseInt(self.options.bottom)) + 'px');
            }
        },

        refreshVisible: function () {
            var self = this;

            if (self.window.scrollTop() > (self.options.correction * 2)) {
                self.element.show('slow');
            } else {
                self.element.hide('slow');
            }
        }
    });
})(jQuery);