var cur_menu = false;
var cur_submenu = false;
var image_link = '/assets/compo/img/admin/storage/alert.gif';

var compo = {};


function objectsAreSame(x, y) {

    var objectsAreSame = true;

    if (x.length != y.length) {
        return false;

    }

    $.each(x, function( index, value ) {


        for(var propertyName in value) {

            if (propertyName == 'price') {
                x[index][propertyName] = parseInt(x[index][propertyName]);
                y[index][propertyName] = parseInt(y[index][propertyName]);
            }

            if(x[index][propertyName] != y[index][propertyName]) {

                objectsAreSame = false;

                break;
            }
        }
    });


    return objectsAreSame;
}

function recountOrder() {
    var delivery = $('.basket_order [name="delivery"]');
    var total = $('.basket_order [name="total"]');

    var total_new = 0;

    $('.basket_order .order-item').each(function () {
        var order_item_tr = $(this);

        var item_price = $('.price', order_item_tr);
        var item_total = $('.total', order_item_tr);
        var item_price_total = $('.price_total', order_item_tr);


        var item_price_total_new = parseInt(item_price.val()) * parseInt(item_total.val());

        item_price_total.val(item_price_total_new);


        total_new += item_price_total_new;
    });

    total_new += parseInt(delivery.val());


    total.val(total_new);
}


function initHandlersForOrderItem(order_item_tr) {
    var delete_btn = $('.delete', order_item_tr);


    delete_btn.click(function () {

        if(confirm("Вы действительно хотите удалить эту позицию?")) {
            order_item_tr.remove();
            recountOrder();
        }

        return false;
    });

    var price = $('.price', order_item_tr);

    price.change(function () {
        recountOrder();
    });

    var total = $('.total', order_item_tr);

    total.change(function () {
        recountOrder();
    });
}


$(function () {

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').attr('action')

    }).on('fileuploadsubmit', function (e, data) {
        data.formData = data.context.find(':input').serializeArray();
    }).on('fileuploadfinished', function (e, data) {
        window.opener.document.location.reload(true);
        window.close();
    });



    /*
     if (window.opener) window.opener.document.location.reload(true);
     <?php else: ?>
     if (window.opener) window.opener.document.location.href = '<?php echo $data['url'];?>';
     <?php endif; ?>
     window.close();
     */
    /*
    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );
    */

    /*
    if (window.location.hostname === 'blueimp.github.io') {
        // Demo settings:
        $('#fileupload').fileupload('option', {
            url: '//jquery-file-upload.appspot.com/',
            // Enable image resizing, except for Android and Opera,
            // which actually support image resizing, but fail to
            // send Blob objects via XHR requests:
            disableImageResize: /Android(?!.*Chrome)|Opera/
                .test(window.navigator.userAgent),
            maxFileSize: 999000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
        });
        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                url: '//jquery-file-upload.appspot.com/',
                type: 'HEAD'
            }).fail(function () {
                $('<div class="alert alert-danger"/>')
                    .text('Upload server currently unavailable - ' +
                        new Date())
                    .appendTo('#fileupload');
            });
        }
    } else {
        // Load existing files:
        //$('#fileupload').addClass('fileupload-processing');

        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: 'dsdsdsdsds', //$('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});
        });

    }

     */



    if ($('.order-form [name="state"] option:selected').val() == 10) {
        //$('.order-form [name="state"] option[value="13"]').attr('selected', 'selected');
    }


    $(window).on('compo.productsSearchAddToOrder.select', function(event, data){
        var new_order_item = $('.basket_order .order-item-template').clone();

        new_order_item.removeClass('order-item-template');
        new_order_item.addClass('order-item');

        // data-id="" data-name="" data-articul="" data-price="" data-total="1" data-href="" data-picture="" data-descript="" data-type="products"
        new_order_item.data('id', data.item.id);
        new_order_item.data('name', data.item.name);

        new_order_item.data('articul', data.item.articul);
        new_order_item.data('price', data.item.price);
        new_order_item.data('href', window.location.origin + '/item/' + data.item.url);
        new_order_item.data('picture', data.item.picture_id);

        $('.item-url', new_order_item).attr('href', new_order_item.data('href'));
        $('.item-url', new_order_item).html(new_order_item.data('name'));
        $('.articul', new_order_item).html(new_order_item.data('articul'));
        $('.price', new_order_item).val(new_order_item.data('price'));
        $('.price_total', new_order_item).val(new_order_item.data('price'));

        initHandlersForOrderItem(new_order_item);


        $('.order-items-list').append(new_order_item);

        recountOrder();

    });
    
    $('.order-item-add-form input[name="keyword"]').productsSearchAddToOrder();

    var delivery = $('.basket_order [name="delivery"]');

    delivery.change(function () {
        recountOrder();
    });


    $('.basket_order .order-item').each(function () {
        var order_item_tr = $(this);

        var order_item_data = order_item_tr.data();

        initHandlersForOrderItem(order_item_tr);
    });

    
    $('.order-form').submit(function (e) {
        e.preventDefault();

        var data = document.order;
        var form_data = $( this ).serializeArray();

        var form_data_obj = {};

        $.each(form_data, function() {
            if (form_data_obj[this.name] !== undefined) {
                if (!form_data_obj[this.name].push) {
                    form_data_obj[this.name] = [form_data_obj[this.name]];
                }
                form_data_obj[this.name].push(this.value || '');
            } else {
                form_data_obj[this.name] = this.value || '';
            }
        });

        var diff = {};

        $.each(form_data_obj, function(index, value) {
            if (index == 'additional_phone') {
                if (value != data.customer_data.additional_phone.join(', ')) {
                    diff['additional_phone'] = {
                        'title': 'Дополнительный телефон',
                        'old': data.customer_data.additional_phone.join(', '),
                        'new': value
                    };
                }
            }

            if (index == 'address') {
                if (value != data.customer_data.address) {
                    diff['address'] = {
                        'title': 'Адрес',
                        'old': data.customer_data.address,
                        'new': value
                    };
                }
            }
/*
            if (index == 'lifting') {
                if (value != data.customer_data.lifting) {
                    diff['lifting'] = {
                        'title': 'Подъём на этаж',
                        'old': data.customer_data.lifting,
                        'new': value
                    };
                }
            }
*/
            if (index == 'email') {
                if (value != data.customer_data.email) {
                    diff['email'] = {
                        'title': 'E-mail',
                        'old': data.customer_data.email,
                        'new': value
                    };
                }
            }

            if (index == 'email') {
                if (value != data.customer_data.email) {
                    diff['email'] = {
                        'title': 'E-mail',
                        'old': data.customer_data.email,
                        'new': value
                    };
                }
            }

            if (index == 'lastname') {
                if (value != data.customer_data.lastname) {
                    diff['lastname'] = {
                        'title': 'ФИО',
                        'old': data.customer_data.lastname,
                        'new': value
                    };
                }
            }


            if (index == 'kilometers') {
                if (value != data.customer_data.kilometers) {
                    diff['kilometers'] = {
                        'title': 'Киллометры',
                        'old': data.customer_data.kilometers,
                        'new': value
                    };
                }
            }

            if (index == 'phone') {
                if (value != data.customer_data.phone) {
                    diff['phone'] = {
                        'title': 'Телефон',
                        'old': data.customer_data.phone,
                        'new': value
                    };
                }
            }

            if (index == 'state') {
                if (value != data.form_data.state) {
                    diff['state'] = {
                        'title': 'Статус',
                        'old_text': document.ostate[data.form_data.state],
                        'new_text': document.ostate[value],
                        'old': data.form_data.state,
                        'new': value
                    };
                }
            }

            if (index == 'total') {
                if (value != data.form_data.total) {
                    diff['total'] = {
                        'title': 'Общая стоимость',
                        'old': data.form_data.total,
                        'new': value
                    };
                }
            }

            if (index == 'delivery') {
                if (value != data.form_data.delivery) {
                    diff['delivery'] = {
                        'title': 'Стоимость доставки',
                        'old': data.form_data.delivery,
                        'new': value
                    };
                }
            }

            if (index == 'delivery_id') {
                if (value != data.form_data.delivery_id) {
                    diff['delivery_id'] = {
                        'title': 'Вид доставки',
                        'old_text': document.dstate[data.form_data.delivery_id],
                        'new_text': document.dstate[value],
                        'old': data.form_data.delivery_id,
                        'new': value
                    };
                }
            }

            if (index == 'payment_id') {
                if (value != data.form_data.payment_id) {
                    diff['payment_id'] = {
                        'title': 'Вид оплаты',
                        'old_text': document.pstate[data.form_data.payment_id],
                        'new_text': document.pstate[value],
                        'old': data.form_data.payment_id,
                        'new': value
                    };
                }
            }
        });




        var new_order_data = [];

        $('.basket_order .order-item').each(function () {
            var order_item_tr = $(this);

            var order_item_data = order_item_tr.data();

            var item_price = $('.price', order_item_tr);
            var item_total = $('.total', order_item_tr);

            order_item_data.price = item_price.val();
            order_item_data.total = item_total.val();


            new_order_data.push(order_item_data);

        });
        
        if (!objectsAreSame(data.order_data, new_order_data)) {

            var new_text = $('<div>');

            $.each(new_order_data, function( index, value ) {

                var row = $('<div>');

                row.append(value['name'] + ': ');
                row.append('<span style="float: right">' + value['price'] + ' x ' + value['total'] + ' </span>');

                new_text.append(row);
            });

            var old_text = $('<div>');

            $.each(data.order_data, function( index, value ) {

                var row = $('<div>');

                row.append(value['name'] + ': ');
                row.append('<span style="float: right">' + value['price'] + ' x ' + value['total'] + ' </span>');


                old_text.append(row);
            });

            diff['order_data'] = {
                'title': 'Состав',
                'old_text': old_text.html(),
                'new_text': new_text.html(),
                'old': data.order_data,
                'new': new_order_data
            };
        }


        
        var diff_html = $('<div class="order-diff">');

        $.each(diff, function(index, value) {

            if (value.old_text == undefined) {
                value.old_text = value.old;
            }
            if (value.new_text == undefined) {
                value.new_text = value.new;
            }

            diff_html.append('<div class="new"><span>' + value.title + '</span>: ' +value.new_text+ '</div>');
            diff_html.append('<div class="old"><span>' + value.title + '</span>: ' +value.old_text+ '</div>');
        });
        
        
        var dialog_form = $('<div id="dialog-form" title="Обновить заказ"> ' +
            '<form class="order-update-form"> ' +
            '<fieldset> ' +
            '<div class="order-diff-wrap">Нет изменений</div>' +
            '</fieldset> ' +
            '</form> ' +
            '</div>');

        if (Object.keys(diff).length > 0) {
            $('.order-diff-wrap', dialog_form).html('<div><strong>Изменения:</strong></div>');

            $('.order-diff-wrap', dialog_form).append(diff_html);
        }


        var update_state_fieldset = $('<fieldset class="update_state">');


        var update_state = $('<select name="update_state">');
        
        update_state.append('<option value="" selected>Не указана</option>');
        update_state.append('<option value="Клиент думает">Клиент думает</option>');

        update_state.append('<option value="Нет в наличии">Нет в наличии</option>');
        update_state.append('<option value="Нет в наличии одного из товаров">Нет в наличии одного из товаров</option>');
        update_state.append('<option value="Нет связи с клиентом">Нет связи с клиентом</option>');
        update_state.append('<option value="Отказ клиента">Отказ клиента</option>');
        update_state.append('<option value="Дублированный заказ">Дублированный заказ</option>');
        update_state.append('<option value="Тестовый заказ">Тестовый заказ</option>');

        update_state.append('<option value="Иное">Иное</option>');

        update_state_fieldset.append('<div><label for="update_state">Причина обновления</label></div>');
        update_state_fieldset.append(update_state);


        $('form', dialog_form).append(update_state_fieldset);

        var update_message_fieldset = $('<fieldset class="update_message">');
        update_message_fieldset.append('<div><label for="update_update_message">Комментарий</label></div>');
        update_message_fieldset.append('<textarea rows="4" name="update_message"></textarea>');

        $('form', dialog_form).append(update_message_fieldset);




        /*
         нет в наличии
         нет в наличии одного из товаров
         нет связи с клиентом
         тестовый заказ
         иное
         */

        var dialog = $( dialog_form ).dialog({
            width: 'auto',
            minWidth: 600,

            height: 'auto',
            modal: true,
            buttons: {
                'Обновить': function() {

                    var update_data = {
                        'id': document.order.form_data.id,
                        'update_state': $( "option:selected",update_state ).val(),
                        'update_message': $( "textarea",update_message_fieldset ).val(),
                        'diff': diff
                    };

                    if (diff['state'] != undefined) {
                        if (diff['state']['new'] == 4) {
                            $(window).trigger("compo.order.refund", [{
                                'item': {
                                    'id': update_data.id
                                }
                            }]);
                        }
                    }

                    $.ajax({
                        type: "POST",
                        url: '/manage.php?op=orders.update',
                        data: update_data,
                        success: function () {
                            dialog.dialog( "close" );

                            window.location.href = '/manage.php?op=orders&created=' + update_data.id;
                            //window.location.reload();
                        }
                    });

                },
                'Отмена': function() {
                    dialog.dialog( "close" );
                }
            },
            close: function() {
            }
        });


        dialog.dialog( "open" );

    })


    $('select').select2({
        width: 'resolve',
        dropdownAutoWidth : true,
       // minimumResultsForSearch: Infinity
    }).on('select2:open', function (e) {
        setTimeout(function () {
            $('[aria-selected="true"]').parent().scrollTop($('[aria-selected="true"]').offset().top);
        }, 100);
    });


    compo.analytics  = $.compo.analytics({
        yandexMetrikaId: window.yandex_metrika_id || '',
        userId: window.userId || null,
        ip: window.ip
    });

    $(window).on("compo.order.refund", function( event, data ) {
        compo.analytics.reachGoal('compo.order.refund', {
            'eventCategory': 'Ecommerce',
            'eventAction': 'Refund',
            'ecommerce': {
                'refund': {
                    'actionField': {'id': data.item.id }
                }
            }
        }, function () {
            if (data.redirect != undefined) {
                window.location.href = data.redirect;

            }
        });
    });



    $(".table-zebra tr:odd").addClass("odd");

    $.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );

    Highcharts.setOptions({
        lang: {
            loading: 'Загрузка...',
            months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
            shortMonths: ['Янв', 'Фев', 'Март', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сент', 'Окт', 'Нояб', 'Дек'],
            exportButtonTitle: "Экспорт",
            printButtonTitle: "Печать",
            rangeSelectorFrom: "С",
            rangeSelectorTo: "По",
            rangeSelectorZoom: "Период",
            downloadPNG: 'Скачать PNG',
            downloadJPEG: 'Скачать JPEG',
            downloadPDF: 'Скачать PDF',
            downloadSVG: 'Скачать SVG',
            printChart: 'Напечатать график'
        }
    });
    $('.show_block').click(function () {
        var block = $("#" + $(this).data('block')).toggle();

        return false;
    });

    // Fix for row width on sortable
    var fixHelper = function (e, ui) {
        ui.children().each(function () {
            $(this).width($(this).width());
        });
        return ui;
    };

    $(".table-droppable").each(function () {
        var table_name = $(this).data('table-name');
        var table_zebra = $(this).hasClass('table-zebra');
        var target = $(this).data('table-target');

        var tbody = $('tbody', this);

        tbody.sortable({
            cancel: '',
            helper: fixHelper,
            items: "tr",
            handle: ".table-droppable-handle",
            sort: function () {
                $(this).removeClass("ui-state-default");
            },
            update: function (event, ui) {
                var after_id = 0;

                if ($(ui.item).prev().length > 0 && $(ui.item).prev().data('id') > 0) {
                    after_id = $(ui.item).prev().data('id');
                }

                var id = $(ui.item).data('id');

                $.get("/manage.php?op=" + table_name + ".switch_position_after_item_action", {
                    id: id,
                    after_id: after_id,
                    table: table_name,
                    target: target
                }, function () {
                    var table = ui.item.parent().parent();
                    var tr = $('tr', table);

                    if (table_zebra) {
                        tr.removeClass('tab1');
                        tr.removeClass('tab2');
                    }

                    tr.removeClass('tab4');

                    $(ui.item).addClass('tab4');

                    if (table_zebra) {
                        $(table).find('tr:odd').addClass('tab1');
                        $(table).find('tr:even').addClass('tab2');
                    }

                    window.location.href = updateQueryStringParameter(window.location.href, 'created', id);
                });
            }
        });
    });


    $('#additional_complects').click(function () {
        $('#additional_complects_block').slideToggle('fast');
    });
    $('#params').click(function () {
        $('#params_block').slideToggle('fast');
    });
    $('#links').click(function () {
        $('#links_block').slideToggle('fast');
    });

    $('#discounts_block').hide();
    $('#discounts').css('text-decoration', 'none');
    $('#discounts').css('border-bottom', '1px dashed');
    $('#discounts').click(function () {
        $('#discounts_block').slideToggle('fast');
    });

    $('#discounts_block2').hide();
    $('#discounts2').css('text-decoration', 'none');
    $('#discounts2').css('border-bottom', '1px dashed');
    $('#discounts2').click(function () {
        $('#discounts_block2').slideToggle('fast');
    });


    $('#alinks').click(function () {
        $('#acomplect_links_block').slideToggle('fast');
    });
    $('#complect_links').click(function () {
        $('#complect_links_block').slideToggle('fast');
    });

    $('#color_links').click(function () {
        $('#color_links_block').slideToggle('fast');
    });
    $('#cats_politics').click(function () {
        $('#cats_block').slideToggle('fast');
    });

    $('#update').click(function () {

        $("input[name='add_action']").val('update');

    });

    $('#update_state').click(function () {

        $("input[name='add_action']").val('update_state');

    });

    $('#add_market').click(function () {

        $("input[name='add_action']").val('add_market');

    });

    $('#changecomplect').click(function () {

        $("input[name='add_action']").val('changecomplect');

    });

    $('#del_market').click(function () {

        $("input[name='add_action']").val('del_market');

    });

    $('#delete').click(function () {

        $("input[name='add_action']").val('delete');

    });

    $('#set_visible').click(function () {

        $("input[name='add_action']").val('set_visible');

    });

    $('#set_invisible').click(function () {

        $("input[name='add_action']").val('set_invisible');

    });

    $('#changecat').click(function () {

        $("input[name='add_action']").val('changecat');

    });


    //Действия когда выбран статус "Снято с производства"
    $('[name="state"]:radio').click(
        function () {

            if ($(this).val() == 30 || $(this).val() == 40) {

                $('#traddtxt').show();
                $('#yml_export').prop("checked", false);

                $('#actions').prop("checked", false);
                $('#hits').prop("checked", false);
                $('#noveltys').prop("checked", false);
                $('#sales').prop("checked", false);
                $('#specials').prop("checked", false);
                $('#populars').prop("checked", false);
            }
            else {
                $('#traddtxt').hide();

            }


        }
    );
});