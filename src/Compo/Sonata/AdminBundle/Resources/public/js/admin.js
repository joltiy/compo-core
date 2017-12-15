function fillmanufactureCollection() {
    if ($('select.manufactureCollection-select2').length) {
        //$('.select2-container').parent().find('select').attr('style','');

        var request_data = {};

        var manufacture_selected = $("select.manufacture-select2 option:selected");

        if (manufacture_selected.length) {
            request_data = {
                manufacture: manufacture_selected.val()
            };
        }


        var manufactureCollection = $("select.manufactureCollection-select2 option:selected").val();


        $.ajax({

            url: Routing.generate('admin_compo_manufacture_manufacturecollection_select2'),
            data: request_data,
            success: function (data) {
                var manufactureCollection_select = $("select.manufactureCollection-select2");

                manufactureCollection_select.select2("destroy");


                manufactureCollection_select.html('');

                manufactureCollection_select.append('<option value=""></option>');

                $.each(data, function (index, value) {
                    var option = $('<option value="' + value.id + '">' + value.text + '</option>');

                    if (value.id == manufactureCollection) {
                        option.attr('selected', 'selected');
                    }

                    manufactureCollection_select.append(option);
                });

                Admin.setup_select2(manufactureCollection_select.parent());

                //$('.select2-container').parent().find('select').attr('style','display:block; position:absolute; bottom: 0; left: 0; clip:rect(0,0,0,0);');
            },
            dataType: 'json'
        });
    }
}

function fillmanufactureCollectionSelect(wrap) {
    if ($('.manufactureCollection-dependet').length) {
        //$('.select2-container').parent().find('select').attr('style','');

        var request_data = {};

        var manufacture_selected = $("input.manufacture-dependet", wrap);

        if (manufacture_selected.length) {
            request_data = {
                manufacture: manufacture_selected.val()
            };
        }

        var manufactureCollection_selected = $("input.manufactureCollection-dependet", wrap);

        if (manufactureCollection_selected.length) {
            var manufactureCollection = manufactureCollection_selected.val();
        } else {
            var manufactureCollection = 0;
        }

        $.ajax({

            url: Routing.generate('admin_compo_manufacture_manufacturecollection_select2'),
            data: request_data,
            success: function (data) {
                var manufactureCollection_select = $("input.manufactureCollection-dependet", wrap);

                manufactureCollection_select.select2("destroy");


                manufactureCollection_select.select2({
                    query: function(options) {
                        var city, i, j, output, paginate_by, results, term;
                        term = options.term.toLowerCase();
                        results = [];
                        output = {};
                        paginate_by = 50;
                        i = 0;
                        j = 0;
                        while (results.length < paginate_by && i < data.length) {
                            i += 1;
                            city = data[i - 1];
                            if (city.text.toLowerCase().indexOf(term) > -1) {
                                j += 1;
                                if (j > (options.page - 1) * paginate_by) {
                                    results.push({
                                        id: city.id,
                                        text: city.text
                                    });
                                }
                            }
                        }
                        output.results = results;
                        output.more = i < data.length;
                        return options.callback(output);
                    },
                    width: function(){
                        // Select2 v3 and v4 BC. If window.Select2 is defined, then the v3 is installed.
                        // NEXT_MAJOR: Remove Select2 v3 support.
                        return Admin.get_select2_width(window.Select2 ? this.element : select);
                    },
                    dropdownAutoWidth: true
                });
            },
            dataType: 'json'
        });
    }
}


function setCookie(key, value) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (24 * 60 * 60 * 1000));
    document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
}

function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
}


function number_format(number, decimals, dec_point, thousands_sep) {	// Format a number with grouped thousands
    //
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +	 bugfix by: Michael White (http://crestidg.com)

    var i, j, kw, kd, km;

    // input sanitation & defaults
    if (isNaN(decimals = Math.abs(decimals))) {
        decimals = 2;
    }
    if (dec_point == undefined) {
        dec_point = ",";
    }
    if (thousands_sep == undefined) {
        thousands_sep = ".";
    }

    i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

    if ((j = i.length) > 3) {
        j = j % 3;
    } else {
        j = 0;
    }

    km = (j ? i.substr(0, j) + thousands_sep : "");
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


    return km + kw + kd;
}

function updateOrderTotal() {
    var total = 0;


    $('div[id*=_elements] td[class*=elements-total] input').each(function () {
        var total_el = $(this);

        total = total + parseFloat(total_el.val().replace(',', '.'));
    });

    var order_total = $('.order_total');

    order_total.val(number_format(total, 2, ',', ''));

    order_total.change();
}

function initOrderElements(subject) {

    $('td[class*=elements-quantity] input', subject).change(function () {
        var quantity_input = $(this);

        var price_input = quantity_input.closest('tr').find('td[class*=elements-price] input').first();
        var total_input = quantity_input.closest('tr').find('td[class*=elements-total] input').first();


        total_input.val(parseFloat(quantity_input.val().replace(',', '.')) * parseFloat(price_input.val().replace(',', '.')));

        total_input.val(number_format(total_input.val(), 2, ',', ''));

        updateOrderTotal();
    });


    $('td[class*=elements-price] input', subject).change(function () {
        var price_input = $(this);

        var quantity_input = price_input.closest('tr').find('td[class*=elements-quantity] input').first();
        var total_input = price_input.closest('tr').find('td[class*=elements-total] input').first();

        total_input.val(parseFloat(quantity_input.val().replace(',', '.')) * parseFloat(price_input.val().replace(',', '.')));

        total_input.val(number_format(total_input.val(), 2, ',', ''));

        updateOrderTotal();
    });


    $('td[class*=elements-total] input', subject).change(function () {
        updateOrderTotal();
    });

}

function initOrderTotal() {
    $('.order_total, .order_delivery_cost').change(function () {
        var order_total = $('.order_total');
        var order_delivery_cost = $('.order_delivery_cost');

        $('.order_total_cost').val(parseFloat(order_total.val().replace(',', '.')) + parseFloat(order_delivery_cost.val().replace(',', '.')));

        $('.order_total_cost').val(number_format($('.order_total_cost').val(), 2, ',', ''));
    });

    $('td[class*=elements-quantity] input').each(function () {
        var quantity_input = $(this);

        var tr = quantity_input.closest('tr');

        initOrderElements(tr);
    });

}

Admin.setup_icheck = function (subject) {
    if (window.SONATA_CONFIG && window.SONATA_CONFIG.USE_ICHECK) {
        Admin.log('[core|setup_icheck] configure iCheck on', subject);

        jQuery("input[type='checkbox']:not('label.btn>input'), input[type='radio']:not('label.btn>input')", subject).each(function () {
            var el = jQuery(this);

            if (!el.hasClass('not-icheck')) {
                el.iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue'
                });

                el.on('ifChanged', function (event) {
                    $(event.target).trigger('change');
                });

            }
        });


    }
};

Admin.add_list_fields = function (subject) {
    Admin.log('[core|add_filters] configure filters on', subject);

    jQuery('a.sonata-toggle-list-field', subject).click(function (e) {
        e.preventDefault();
        e.stopPropagation();

        var item = $(this);

        if (item.hasClass("required-list-field")) {
            return false;
        }

        item.toggleClass('active');

        if (item.hasClass("active")) {
            item.find('.fa')
                .removeClass('fa-square-o')
                .addClass('fa-check-square-o')
            ;
        } else {
            item.find('.fa')
                .removeClass('fa-check-square-o')
                .addClass('fa-square-o')
            ;
        }
    });

    jQuery('a.sonata-toggle-list-field-apply', subject).click(function (e) {
        var item = $(this);

        var items = item.parent().parent().find('.active');
        var code = item.parent().parent().data('code');

        var data = {
            'code': code,
            'fields': []
        };

        items.each(function () {
            var el = $(this);

            data.fields.push(el.data('field-name'));
        });


        $.ajax({
            method: "POST",
            url: Routing.generate('compo_core_update_user_settings'),
            data: data
        })
            .done(function (msg) {
                window.location.reload();
            });

    });


};


Admin.shared_setup = function (subject) {
    Admin.log("[core|shared_setup] Register services on", subject);
    Admin.set_object_field_value(subject);
    Admin.add_filters(subject);
    Admin.add_list_fields(subject);

    Admin.setup_select2(subject);
    Admin.setup_icheck(subject);
    Admin.setup_xeditable(subject);
    Admin.setup_form_tabs_for_errors(subject);
    Admin.setup_inline_form_errors(subject);
    Admin.setup_tree_view(subject);
    Admin.setup_collection_counter(subject);
    Admin.setup_sticky_elements(subject);

    setupStatsDimensions(subject);

//        Admin.setup_list_modal(subject);

    $('.page-composer__container__child__name__input').addClass('form-control');

    initOrderElements(subject);
};


$(document).ready(function () {
    Highcharts.setOptions({
        global: {
            useUTC: false,
        },
        lang: {
            loading: 'Загрузка...',
            months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
            shortMonths: ['Янв', 'Фев', 'Март', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сент', 'Окт', 'Нояб', 'Дек'],
            exportButtonTitle: "Экспорт",
            printButtonTitle: "Печать",
            rangeSelectorFrom: "С",
            rangeSelectorTo: "По",
            rangeSelectorZoom: "Детализация",
            downloadPNG: 'Скачать PNG',
            downloadJPEG: 'Скачать JPEG',
            downloadPDF: 'Скачать PDF',
            downloadSVG: 'Скачать SVG',
            printChart: 'Напечатать график'
        }
    });

    initOrderTotal();
    $('.sidebar-toggle').click(function () {
        var body = $('body');

        if (body.data('collapse') == 0) {
            body.data('collapse', 1);
            setCookie('admin_sidebar_collapse', 1);
        } else {
            body.data('collapse', 0);
            setCookie('admin_sidebar_collapse', 0);
        }
    });

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


    /*

     $(".sonata-ba-collapsed-fields [name*=enabled],.sonata-ba-collapsed-fields [name*=noIndexEnabled],.sonata-ba-collapsed-fields [name*=yandexMarketEnabled]")
     .bootstrapSwitch({
     size: 'mini',
     onText: 'да',
     offText: 'нет'
     })
     .closest('.icheckbox_square-blue')
     .css('background-image', 'none');

     */


    var tab_active = $('.sonata-ba-form .nav-tabs li.active a');

    if (tab_active.length > 0) {
        var form = $(tab_active).closest("form");
        var el = $(tab_active);

        form.attr('action', form.attr('action') + '&current_tab_index=' + el.data('index'));

        //history.replaceState(history.state, document.title, event.target.href);
    }


    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        $('.CodeMirror').each(function (i, el) {
            el.CodeMirror.refresh();
        });
    });


    $(document).on('click', '.sonata-ba-form .nav-tabs a', function (event) {

        var form = $(event.target).closest("form");
        var el = $(event.target);

        form.attr('action', form.attr('action') + '&current_tab_index=' + el.data('index'));

        history.replaceState(history.state, document.title, event.target.href);
    });


    $('.product-catalog-tree li').each(function () {
        var badge = $('.badge-count', this);

        var badge_first = $('.badge-count', this).first();

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
            } else if ((lvl !== lvl_old || position == 'append') && root == target) {

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

    $('input.manufacture-dependet').on("change", function (e) {
        fillmanufactureCollectionSelect($(this).closest('.actionFormModal'));
    });


    // $("select.manufactureCollection-select2 option:selected")


    //fillmanufactureCollection();


    CodeMirror.defineMode("htmltwig", function (config, parserConfig) {
        return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || "text/html"), CodeMirror.getMode(config, "twig"));
    });


    $('.highlight-src').each(function () {
        var el = $(this);


        var myCodeMirror = CodeMirror.fromTextArea(el.get(0), {
            lineNumbers: true,
            mode: "htmltwig",
            lineWrapping: true,
            indentWithTabs: false
        });


        myCodeMirror.on("blur", function () {
            myCodeMirror.save()
        });

        setTimeout(function () {
            myCodeMirror.refresh();
        }, 1);
    });


    //$('.select2-container').parent().find('select').attr('style','display:block; position:absolute; bottom: 0; left: 0; clip:rect(0,0,0,0);');


    $('.btn_batch').click(function (e) {

        var value = $('.select-batchactions option:selected').val();

        if ($('#actionForm' + value).length) {
            $('.actionFormModal-required').prop('required', true);

            e.preventDefault();

            $('#actionForm' + value).modal('show');
            return false;
        } else {
            var requiredFields = $('.actionFormModal :required');
            requiredFields.addClass('actionFormModal-required');
            requiredFields.prop('required', false);

            return true;
        }
    });


});


function createChartStock(tableCharts, id, column_id, dimensions_size, timeline) {
    var columns = [];

    //console.log(column_id);
    //console.log(dimensions_size);


    $('thead th', tableCharts).each(function () {
        var th = $(this);

        var item = {};

        item.type = th.data('type');
        item.filed_type = th.data('field-type');
        item.label = th.text().trim();

        columns.push(item);
    });


    var series_obj = {};

    if (timeline) {
        var total_data = {};

    } else {
        var total_data = 0;

    }

    if (timeline && dimensions_size == 2) {
        var series_name = $('th:nth-child(2)', tableCharts).text().trim();
    } else {
        var series_name = '';

        for (var i = 2; i <= dimensions_size; i++) {
            series_name = series_name + ' ' + $('th:nth-child(' + i + ')', tableCharts).text().trim();
        }

        series_name = series_name.trim();
    }

    $('tbody tr', tableCharts).each(function () {
        var item = [];

        var tr = $(this);

        if (timeline) {
            var date = parseInt($('td:nth-child(1)', tr).data('raw')) * 1000;
        }

        if (timeline && dimensions_size == 2) {
            var series_name = $('td:nth-child(2)', tr).text().trim();
        } else {
            var series_name = '';

            for (var i = 2; i <= dimensions_size; i++) {
                series_name = series_name + ' ' + $('td:nth-child(' + i + ')', tr).text().trim();
            }

            series_name = series_name.trim();
        }

        var value = parseInt($('td:nth-child(' + column_id + ')', tr).text().trim());


        if (series_obj[series_name] == undefined) {
            series_obj[series_name] = {
                'name': series_name,
                'data': [],
                'connectNulls': true,
                'connectEnds': true
            };
        }

        if (timeline) {
            series_obj[series_name].data.push([date, value]);

            if (total_data[date] == undefined) {
                total_data[date] = {
                    'date': date,
                    'value': 0
                };
            }

            total_data[date].date = date;
            total_data[date].value += value;

        } else {
            series_obj[series_name].data.push([value]);

            total_data += value;
        }

    });

    if (timeline) {
        var total_data_array = [];

        $.each(total_data, function (index, value) {

            total_data_array.push([value.date, value.value]);
        });


        series_obj['Всего'] = {
            'name': 'Всего',
            'data': total_data_array,
            'connectNulls': true,
            'connectEnds': true
        };
    } else {
        series_obj['Всего'] = {
            'name': 'Всего',
            'data': [total_data],
            'connectNulls': true,
            'connectEnds': true
        };
    }


    var series = [];

    $.each(series_obj, function (index, value) {
        if (timeline) {
            var unordered = {};

            $.each(value.data, function (index_row, value_row) {
                unordered[value_row[0]] = value_row;
            });

            value.data = [];


            Object.keys(unordered).sort().forEach(function(key) {
                value.data.push(unordered[key]);
            });

            series.push(value);

        } else {
            series.push(value);
        }
    });


    Highcharts.stockChart('chart-' + id, {
        chart: {
            type: 'spline',
        },
        rangeSelector: {
            inputDateFormat: '%d.%m.%Y',
            inputEditDateFormat: '%d.%m.%Y',

            buttonTheme: {
                width: 50,
            },
            allButtonsEnabled: true,
            buttons: [
                {
                    type: 'week',
                    count: 1,
                    text: 'День',
                    dataGrouping: {
                        forced: true,
                        units: [['day', [1]]]
                    }
                },
                {
                    type: 'month',
                    count: 4,
                    text: 'Неделя',
                    dataGrouping: {
                        forced: true,
                        units: [['week', [1]]]
                    }
                },
                {
                    type: 'year',
                    count: 12,
                    text: 'Месяц',
                    dataGrouping: {
                        forced: true,
                        units: [['month', [1]]]
                    }
                }
            ],
            selected: 0
        },
        title: {
            text: ''
        },

        xAxis: {
            type: 'datetime'
        },

        yAxis: {
            title: {
                text: $('th:nth-child(' + column_id + ')', tableCharts).text().trim()
            }
        },

        legend: {
            enabled: true,
            align: 'right',
            backgroundColor: '#FCFFC5',
            borderColor: 'black',
            borderWidth: 2,
            layout: 'vertical',
            verticalAlign: 'middle',
            shadow: true

        },

        plotOptions: {
            series: {
                dataGrouping: {
                    approximation: 'sum',
                },
                allowPointSelect: true,
                label: {
                    connectorAllowed: false
                },

                showInNavigator: true,
                connectNulls: true,

                line: {
                    connectNulls: true,
                },
                spline: {
                    connectNulls: true,
                }
            },
            line: {
                connectNulls: true,
            },
            spline: {
                connectNulls: true,
            }
        },

        series: series,
        tooltip: {
            shared: false,
            crosshairs: false,
            split: true

        },
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }

    }, function (chart) {
        setTimeout(function () {
            $('input.highcharts-range-selector', $(chart.container).parent())
                .datepicker({
                    regional: $.datepicker.regional['ru'],
                    dateFormat: 'dd.mm.yy',
                    onSelect: function () {
                        this.onchange();
                        this.onblur();
                    }
                });
        }, 0);
    });
}


function createChart(tableCharts, id, column_id, dimensions_size, ignore) {
    var columns = [];

    if (ignore == undefined) {
        ignore = [];
    }

    $('thead th', tableCharts).each(function () {
        var th = $(this);

        var item = {};

        item.type = th.data('type');
        item.filed_type = th.data('field-type');
        item.label = th.text().trim();

        columns.push(item);
    });

    var series_obj = {};

    var yAxis_label = $('th:nth-child(' + column_id + ')', tableCharts).text().trim();

    var xAxis_label = $('th:nth-child(' + 1 + ')', tableCharts).text().trim();


    var categories = {};

    var series_columns = {};
    var series_data = {};

    $('tbody tr', tableCharts).each(function () {
        var tr = $(this);

        if (!ignore.indexOf($('td:nth-child(1)', tr).text().trim())) {
            return;
        }

        categories[$('td:nth-child(1)', tr).text().trim()] = $('td:nth-child(1)', tr).text().trim();

        var value = parseInt($('td:nth-child(' + column_id + ')', tr).text().trim());

        if (dimensions_size > 1) {
            series_columns[$('td:nth-child(1)', tr).text().trim()] = $('td:nth-child(1)', tr).text().trim();
        }


        if (dimensions_size > 1) {
            if (series_data[$('td:nth-child(2)', tr).text().trim()] == undefined) {
                series_data[$('td:nth-child(2)', tr).text().trim()] = {};
            }
        } else {
            if (series_data[$('td:nth-child(1)', tr).text().trim()] == undefined) {
                series_data[$('td:nth-child(1)', tr).text().trim()] = {};
            }
        }

        if (dimensions_size > 1) {
            series_data[$('td:nth-child(2)', tr).text().trim()][$('td:nth-child(1)', tr).text().trim()] = value;
        } else {
            series_data[$('td:nth-child(1)', tr).text().trim()] = value;
        }
    });


    var series = [];

    if (dimensions_size > 1) {


        $.each(series_data, function (index, value) {

            var series_item = {
                name: yAxis_label,
                data: []
            };
            series_item.name = index;

            $.each(series_columns, function (series_columns_index, series_columns_value) {
                if (value[series_columns_index] == undefined) {
                    series_item.data.push(0);
                } else {
                    series_item.data.push(value[series_columns_index]);
                }
            });

            series.push(series_item);

        });

    } else {
        var data = [];

        $.each(series_data, function (index, value) {
            data.push(value);
        });

        series.push({
            name: yAxis_label,

            type: 'column',
            colorByPoint: true,
            showInLegend: false,
            data: data,
        });
    }


    var categories_array = [];

    $.each(categories, function (index, value) {
        categories_array.push(value);
    });


    Highcharts.chart('chart-' + id, {
        chart: {
            type: 'column',
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: categories_array,
            crosshair: true,
            title: {
                text: xAxis_label
            }
        },

        yAxis: {
            title: {
                text: yAxis_label
            }
        },

        legend: {
            enabled: true,
            align: 'right',
            backgroundColor: '#FCFFC5',
            borderColor: 'black',
            borderWidth: 2,
            layout: 'vertical',
            verticalAlign: 'middle',
            shadow: true
        },

        plotOptions: {
            series: {
                showInNavigator: true,
                connectNulls: true,
            }
        },

        series: series,
    });






}


function setupStatsDimensions(subject) {
    $('.form-stats-entity', subject).each(function () {
        var entity = $(this);

        entity.change(function () {
            // ... retrieve the corresponding form.
            var $form = $(this).closest('form');
            // Simulate form data, but only include the selected sport value.
            var data = {};
            data[entity.attr('name')] = entity.val();
            // Submit data via AJAX to the form's action path.

            data['get_dimensions'] = 1;
            data['entity'] = entity.val();

            $.ajax({
                url : $form.attr('action'),
                type: 'GET',
                data : data,
                success: function(html) {
                    // Replace current position field ...
                    $('.form-stats-dimensions', $form).parent().html(
                        // ... with the returned one from the AJAX response.
                        $(html).find('.form-stats-dimensions').parent().html()
                    );

                    $('.form-stats-metrics', $form).parent().html(
                        // ... with the returned one from the AJAX response.
                        $(html).find('.form-stats-metrics').parent().html()
                    );

                    //Admin.setup_collection_counter(subject);

                    //Admin.setup_collection_buttons(subject);

                    // Position field now displays the appropriate positions.
                }
            });
        });
    });
}
