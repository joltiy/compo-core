Admin.setup_icheck = function (subject) {
    if (window.SONATA_CONFIG && window.SONATA_CONFIG.USE_ICHECK) {
        Admin.log('[core|setup_icheck] configure iCheck on', subject);

        jQuery("input[type='checkbox']:not('label.btn>input'), input[type='radio']:not('label.btn>input')", subject).each(function () {
            var el = jQuery(this);

            if (!el.hasClass('not-icheck')) {
                el.iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue'
                });

                el.on('ifChanged', function (event) {
                    $(event.target).trigger('change');
                });
            }
        });
    }
};
