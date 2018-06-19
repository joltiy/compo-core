$(function ($) {
    $(window).on("compo.sonata.admin.shared_setup", function( event, data ) {
        Admin.log('[compo|sortable] on', data.subject);

        $(".table-sortable", data.subject).each(function () {
            var sortable_action = $(this).data('sortable-action');

            var tbody = $('tbody', this);

            tbody.sortable({
                cancel: '',
                helper: function (e, ui) {
                    ui.children().each(function () {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                items: "tr",
                handle: ".table-sortable-handle",
                sort: function () {
                    $(this).removeClass("ui-state-default");
                },
                update: function (event, ui) {
                    var after_id = 0;

                    if ($(ui.item).prev().length > 0 && $(ui.item).prev().data('id') > 0) {
                        after_id = $(ui.item).prev().data('id');
                    }

                    var id = $(ui.item).data('id');

                    $.post(sortable_action, {
                        id: id,
                        after_id: after_id
                    }, function () {

                    });
                }
            });
        });

        $('.table-th-_delete', data.subject).click(function () {
            var el = $(this);

            var state = el.data('select-all');

            var checkbox = $(el).closest('.table').find('[name*=_delete]');

            if (parseInt(state) === 1) {
                checkbox.iCheck('uncheck');
                el.data('select-all', 0);
            } else {
                checkbox.iCheck('check');
                el.data('select-all', 1);
            }
        });


    });
});
