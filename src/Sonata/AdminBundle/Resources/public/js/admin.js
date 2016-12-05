function fillmanufactureCollection() {
    if ($(".manufacture-select2 option:selected").length) {
        var request_data = {
            manufacture: $(".manufacture-select2 option:selected").val()
        };
    } else {
        var request_data = {};
    }

    var manufactureCollection = $("select.manufactureCollection-select2 option:selected").val();

    $.ajax({
        url: document.admin_compo_manufacture_manufacturecollection_select2,
        data: request_data,
        success: function (data) {
            $("select.manufactureCollection-select2").select2("destroy");


            $("select.manufactureCollection-select2").html('');

            $("select.manufactureCollection-select2").append('<option value=""></option>');

            $.each(data, function( index, value ) {
                var option = $('<option value="'+value.id+'">' + value.text + '</option>');

                if (value.id == manufactureCollection) {
                    option.attr('selected', 'selected');
                }

                $("select.manufactureCollection-select2").append(option);
            });

            Admin.setup_select2($("select.manufactureCollection-select2").parent());
        },
        dataType: 'json'
    });
}




function setCookie(key, value) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
    document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
}
function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
}




function initOrderElements() {
    $('div[id*=_elements] td[class*=elements-quantity] input').change(function(){
        var quantity_input = $(this);


        var price_input = quantity_input.closest('tr').find('td[class*=elements-price] input').first();
        var total_input = quantity_input.closest('tr').find('td[class*=elements-total] input').first();

        total_input.val(parseInt(quantity_input.val()) * parseFloat(price_input.val()));

        total_input.change();
    });



    $('div[id*=_elements] td[class*=elements-price] input').change(function(){
        var price_input = $(this);


        var quantity_input = price_input.closest('tr').find('td[class*=elements-quantity] input').first();
        var total_input = price_input.closest('tr').find('td[class*=elements-total] input').first();

        total_input.val(parseInt(quantity_input.val()) * parseFloat(price_input.val()));

        total_input.change();
    });

    $('div[id*=_elements] td[class*=elements-total] input').change(function(){

        var total = 0;


        $('div[id*=_elements] td[class*=elements-total] input').each(function () {
            var total_el = $(this);

            total = total + parseFloat(total_el.val());

        });

        var order_total = $('.order_total');

        order_total.val(total);


        var order_delivery_cost = $('.order_delivery_cost');



        $('.order_total_cost').val(parseFloat(order_delivery_cost.val()) + total);

    });

}


Admin.shared_setup = function(subject) {
    Admin.log("[core|shared_setup] Register services on", subject);
    Admin.set_object_field_value(subject);
    Admin.add_filters(subject);
    Admin.setup_select2(subject);
    Admin.setup_icheck(subject);
    Admin.setup_xeditable(subject);
    Admin.setup_form_tabs_for_errors(subject);
    Admin.setup_inline_form_errors(subject);
    Admin.setup_tree_view(subject);
    Admin.setup_collection_counter(subject);
    Admin.setup_sticky_elements(subject);

//        Admin.setup_list_modal(subject);

    $('.page-composer__container__child__name__input').addClass('form-control');

    initOrderElements();
};




$(document).ready(function () {

    initOrderElements();





    $('.order_delivery_cost').change(function () {

        var order_delivery_cost = $('.order_delivery_cost');
        var order_total = $('.order_total');

        $('.order_total_cost').val(parseFloat(order_delivery_cost.val()) + parseFloat(order_total.val()));
    });








    // Fix for row width on sortable
    var fixHelper = function (e, ui) {
        ui.children().each(function () {
            $(this).width($(this).width());
        });
        return ui;
    };

    $(".table-sortable").each(function () {
        var sortable_action = $(this).data('sortable-action');

        var tbody = $('tbody', this);

        tbody.sortable({
            cancel: '',
            helper: fixHelper,
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

























    $('.table-th-_delete').click(function () {
        var el = $(this);

        var state = el.data('select-all');

        var checkbox = $(el).closest('.table').find('[name*=_delete]');

        if (state == 1) {
            checkbox.iCheck('uncheck');
            el.data('select-all', 0);
        } else {
            checkbox.iCheck('check');
            el.data('select-all', 1);
        }
    });






    $(".sonata-ba-collapsed-fields [name*=enabled],.sonata-ba-collapsed-fields [name*=noIndexEnabled],.sonata-ba-collapsed-fields [name*=yandexMarketEnabled]")
        .bootstrapSwitch({
            size: 'mini',
            onText: 'да',
            offText: 'нет'
        })
        .closest('.icheckbox_square-blue')
        .css('background-image', 'none');





    var tab_active = $('.sonata-ba-form .nav-tabs li.active a');

    if (tab_active.length > 0) {
        var form = $( tab_active ).closest( "form" );
        var el = $( tab_active );

        form.attr('action', form.attr('action') + '&current_tab_index=' + el.data('index'));

        history.replaceState(history.state, document.title, event.target.href);
    }


    $(document).on('click', '.sonata-ba-form .nav-tabs a', function (event) {

        var form = $( event.target ).closest( "form" );
        var el = $( event.target );

        form.attr('action', form.attr('action') + '&current_tab_index=' + el.data('index'));

        history.replaceState(history.state, document.title, event.target.href);
    });










    $('.product-catalog-tree li').each(function () {
        var badge = $('.badge', this);

        var badge_first = $('.badge', this).first();

        var count = 0;

        badge.each(function () {
            if (this.index == 0) {
                return;
            }
            count = parseInt(count) + parseInt($(this).text());
        });

        if (badge.length > 1) {
            badge_first.text(count);
        }
    });



    $('.tree ul.tree-list').nestedSortable({
        handle: '.sort',
        items: 'li',
        toleranceElement: '> div',
        listType: 'ul',

        protectRoot: false,
        isTree: true,
        startCollapsed: true,
        expandOnHover: true,
        expandedClass: 'opened',
        disableParentChange: false,

        placeholder: 'node-placeholder',
        stop: function (event, ui) {

            var parent = ui.item.parent().children().find('.node-item').data('id'),
                prev = ui.item.prev().children().find('.node-item').data('id'),
                next = ui.item.next().children().find('.node-item').data('id'),
                position = false,
                target = false;


            if (prev) {
                position = 'after';
                target = prev;
            } else if (next) {
                position = 'before';
                target = next;
            } else if (parent) {
                position = 'append';
                target = parent;
            }

            var id = ui.item.find('.node-item').first().data('id');



            ui.item.css('background', '#f1f5c9');


            var lvl = ui.item.parent().parent().children().find('.node-item').data('lvl');
            var lvl_old = ui.item.parent().children().find('.node-item').data('lvl');


            //if (lvl == 0) {
            //    console.log(lvl);
            //    return false;
            //}

            var root = ui.item.parent().parent().find('.node-item').first().data('id');

            if ((lvl !== lvl_old || position == 'append') && root != target) {

                $.get(ui.item.data('move-url') + '&target=' + root + '&position=append', function (data) {

                    ui.item.parent().children().find('.node-item').data('lvl', lvl + 1);

                    if (ui.item.parent().parent().children().find('.node-item').length > 2 && root != target && target != id) {
                        $.get(ui.item.data('move-url') + '&target=' + target + '&position=' + position, function (data) {
                            if (data.result) {
                                ui.item.css('background', 'white');
                            } else {
                                ui.item.css('background', '#f5c9c9');
                            }
                        });
                    }

                });
            } else if((lvl !== lvl_old || position == 'append') && root == target) {

            } else if (target != id) {
                $.get(ui.item.data('move-url') + '&target=' + target + '&position=' + position, function (data) {
                    if (data.result) {
                        ui.item.css('background', 'white');
                    } else {
                        ui.item.css('background', '#f5c9c9');
                    }
                });
            }

            $('ul.tree-list li').each(function () {
                if (!$(this).find('li').length) {
                    if ($(this).hasClass('opened')) {
                        $('> .node > .node-item > i', this).removeClass('fa-folder').addClass('fa-folder-o');
                    } else {
                        $('> .node > .node-item > i', this).removeClass('fa-folder').addClass('fa-folder-o');
                    }
                } else {
                    if ($(this).hasClass('opened')) {
                        $('> .node > .node-item > i', this).removeClass('fa-folder-o').addClass('fa-folder-open');
                    } else {
                        $('> .node > .node-item > i', this).removeClass('fa-folder-o').addClass('fa-folder');
                    }
                }
            });
        }
    });





    $('a.open-tree').each(function () {
        if (getCookie($(this).data('alias') + '.' + $(this).data('id')) == 'opened') {
            $(this).find('i').removeClass('fa-folder').addClass('fa-folder-open');
            $(this).parent().parent().addClass('opened');
        }
    });


    $(document).on('click', 'a.node-item', function () {
        if ($(this).find('i').hasClass('fa-folder')) {
            setCookie($(this).data('alias') + '.' + $(this).data('id'), 'opened');
            $(this).find('i').removeClass('fa-folder').addClass('fa-folder-open');
            $(this).parent().parent().addClass('opened');
        } else {
            setCookie($(this).data('alias') + '.' + $(this).data('id'), 'closed');
            $(this).find('i').removeClass('fa-folder-open').addClass('fa-folder');
            $(this).parent().parent().removeClass('opened');
        }
        return false;
    });










    $(".manufacture-select2").on("change", function (e) {
        fillmanufactureCollection();
    });

    fillmanufactureCollection();


});

