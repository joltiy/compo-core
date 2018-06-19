$(function ($) {
    $('.sidebar-toggle').click(function () {
        var body = $('body');

        if (body.data('collapse')) {
            body.data('collapse', 0);
            $.ajax({
                method: "POST",
                url: Routing.generate('compo_core_update_user_settings'),
                data: {
                    'settings': {
                        'admin_sidebar_collapse': 0
                    }
                }
            });
        } else {
            body.data('collapse', 1);
            $.ajax({
                method: "POST",
                url: Routing.generate('compo_core_update_user_settings'),
                data: {
                    'settings': {
                        'admin_sidebar_collapse': 1
                    }
                }
            });
        }
    });
});
