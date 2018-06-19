Admin.shared_setup = function (subject) {
    Admin.log("[core|shared_setup] Register services on", subject);
    Admin.set_object_field_value(subject);
    Admin.add_filters(subject);
    Admin.setup_select2(subject);
    Admin.setup_icheck(subject);
    Admin.setup_checkbox_range_selection(subject);
    Admin.setup_xeditable(subject);
    Admin.setup_form_tabs_for_errors(subject);
    Admin.setup_inline_form_errors(subject);
    Admin.setup_tree_view(subject);
    Admin.setup_collection_counter(subject);
    Admin.setup_sticky_elements(subject);
    Admin.setup_readmore_elements(subject);

    $(document).ready(function () {
        $(document).trigger("compo.sonata.admin.shared_setup", [{
            'subject': subject
        }]);
    });


};

$(document).ready(function () {
    //$('.select2-container').parent().find('select').attr('style','display:block; position:absolute; bottom: 0; left: 0; clip:rect(0,0,0,0);');
});
