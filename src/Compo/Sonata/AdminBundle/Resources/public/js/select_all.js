$(function ($) {
    $(window).on("compo.sonata.admin.shared_setup", function( event, data ) {
        Admin.log('[compo|select-all] on', data.subject);

        $('.select-all-btn', data.subject).on('ifChanged change', function () {
            var checkboxes = $(this).closest('.select-all-wrap').find(':checkbox').not($(this));

            if (window.SONATA_CONFIG && window.SONATA_CONFIG.USE_ICHECK) {
                checkboxes.iCheck($(this).is(':checked') ? 'check' : 'uncheck');
            } else {
                checkboxes.prop('checked', $(this).is(':checked'));
            }
        });
    });
});
