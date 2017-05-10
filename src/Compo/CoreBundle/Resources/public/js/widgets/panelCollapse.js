(function ($) {
    $.widget("compo.panelCollapse", {
        options: {
            scroll: true
        },
        _create: function () {
            var self = this;

            if (self.options.scroll) {
                $('.btn-list-buttons, ul.list-group.list-group-links.list-group-links-lists-tree', self.element).css('min-height', '280px');


                $('.btn-list-buttons, ul.list-group.list-group-links.list-group-links-lists-tree', self.element).jScrollPane({
                    autoReinitialise: true
                });
            }




            self.element.on('shown.bs.collapse', function (event) {
                $(this).parent().find(".panel-title .font-icon").removeClass("fa-chevron-right").addClass("fa-chevron-down");

                self._trigger("show", null, {
                    name: $(event.target).attr('id')
                });

            }).on('hidden.bs.collapse', function () {
                $(this).parent().find(".panel-title .font-icon").removeClass("fa-chevron-down").addClass("fa-chevron-right");
            });

        }

    });
})(jQuery);