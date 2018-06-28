$(function ($) {

    $(window).on("compo.sonata.admin.shared_setup", function( event, data ) {
        $(".menu-type-select").on("change", function () {

            var select = $(this);

            var type = select.val();

            $.ajax({
                url: Routing.generate('menu_target_items'),
                data: {
                    type: type
                },
                success: function (data) {
                    var menuTarget = $('.menu-target-id');

                    menuTarget.select2("destroy");

                    menuTarget.html('');

                    menuTarget.append('<option value=""></option>');

                    $.each(data, function (index, value) {
                        var option = $('<option value="' + value.id + '">' + value.text + '</option>');

                        menuTarget.append(option);
                    });

                    Admin.setup_select2(menuTarget.parent());
                },
                dataType: 'json'
            });
        });
    });
});
