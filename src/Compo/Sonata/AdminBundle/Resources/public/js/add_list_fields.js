function add_list_fields(subject) {

    Admin.log('[compo|add_list_fields] configure filters on', subject);

    jQuery('a.sonata-toggle-list-field', subject).click(function (e) {
        e.preventDefault();
        e.stopPropagation();

        var item = $(this);

        if (item.hasClass("required-list-field")) {
            return false;
        }

        item.toggleClass('active');

        var fa = item.find('.fa');

        if (item.hasClass("active")) {
            fa.removeClass('fa-square-o').addClass('fa-check-square-o');
        } else {
            fa.removeClass('fa-check-square-o').addClass('fa-square-o');
        }
    });

    jQuery('a.sonata-toggle-list-field-apply', subject).click(function () {
        var item = $(this);

        var items = item.parent().parent().find('.active');
        var code = item.parent().parent().data('code');

        var setting_name = code + '.list.fields';

        var data = {};

        data[setting_name] = [];

        items.each(function () {
            var el = $(this);

            data[setting_name].push(el.data('field-name'));
        });


        $.ajax({
            method: "POST",
            url: Routing.generate('compo_core_update_user_settings'),
            data: {
                'settings': data
            }
        })
            .done(function () {
                window.location.reload();
            });
    });
}

$(function ($) {
    $(window).on("compo.sonata.admin.shared_setup", function( event, data ) {
        add_list_fields(data.subject);
    });
});
