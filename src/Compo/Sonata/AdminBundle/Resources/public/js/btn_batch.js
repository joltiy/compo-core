$(function ($) {
    $('.btn_batch').click(function (e) {

        var value = $('.select-batchactions option:selected').val();

        var form = $('#actionForm' + value);

        if (form.length) {
            $('.actionFormModal-required').prop('required', true);

            e.preventDefault();

            form.modal('show');

            return false;
        } else {
            var requiredFields = $('.actionFormModal :required');

            requiredFields.addClass('actionFormModal-required');
            requiredFields.prop('required', false);

            return true;
        }
    });
});
