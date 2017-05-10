(function ($) {
    $.widget("compo.advantages", {

        _create: function () {
            var self = this;

            self.element.on('show.bs.modal', function (event) {
                var modal = $(this);

                var button      = $(event.relatedTarget);
                var recipient   = button.data('target-tab');
                var target      = $('a[href="#' + recipient + '"]', modal);

                target.tab('show');

                self._trigger("showModal", null, {
                    event: event,
                    modal: modal,
                    button: button,
                    recipient: recipient,
                    targetEl: target
                });
            });
        }

    });
})(jQuery);