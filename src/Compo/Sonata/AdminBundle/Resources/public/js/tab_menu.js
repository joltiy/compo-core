$(function ($) {
    // show active tab on reload
    if (location.hash !== '' && location.hash.substring(1, 5) === 'tab_') {
        var currentTabNumber = location.hash.substring(5);
        var adminUniqid      = $('.admin_base_edit_form').data('uniqid');

        $('a[href="#tab_' + adminUniqid + '_' + currentTabNumber + '"]').tab('show');
    }

    // remember the hash in the URL without jumping
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var tabName     = $(e.target).attr('href').substring(1);
        var tabParts    = tabName.match(/tab_\w+_(\d)/);
        var tabHashName = '#tab_' + tabParts[1];

        if(history.pushState) {
            history.pushState(null, null, tabHashName);

        } else {
            location.hash = tabHashName;
        }
    });

    // add the hash in action form when submit
    $('form').on('submit', function () {
        if (location.hash.substring(1, 5) === 'tab_') {
            $(this).attr('action', $(this).attr('action') + location.hash)
        }
    });
});
