function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
        return uri + separator + key + "=" + value;
    }
}

function closeAndReload() {


    if (window.opener) window.opener.document.location.reload(true);
    window.close();


}

function changePrices() {
    var pstring = $('#percents').val();

    if (pstring) {

        var operator = pstring.substr(0, 1);
        var percents = pstring.substr(1);
        percents = percents * 1 / 100;


        if (operator != '+' && operator != '-') {
            alert('Неверный формат операции!');
        }
        else {
            if (operator == '+') {
                $(".prices").each(function () {

                    var new_val = $(this).val() * 1 + $(this).val() * 1 * percents;
                    $(this).val(new_val.toFixed(2))

                });


            }
            if (operator == '-') {
                $(".prices").each(function (indx) {
                    $(".oldprices").eq(indx).val($(this).val() * 1);

                    var new_val = $(this).val() * 1 - $(this).val() * 1 * percents;
                    $(this).val(new_val.toFixed(2))

                });


            }

        }
    }
    else {
        alert('Вы ничего не ввели');

    }


}

function updatePoliticsCats() {
    var url = "/manage.php?op=ajax.updatePoliticsCats&";
    $.get(
        url + $('#form_cats').serialize(),
        {
            process: 1

        },
        onPricesSuccess
    );
}

function modifyColor(checkbox, parent, table) {

    var url = "/manage.php?op=ajax." + (checkbox.checked == true ? 'addColor' : 'deleteColor');


    $.get(
        url,
        {
            id: checkbox.value,
            parent: parent,
            table: table

        }
    );


}

function set_market(mode) {
    if (mode == 1) {
        $('input.market').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', true);
            }
        });
    }

    if (mode == 2) {
        $('input.market').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', false);
            }
        });
    }
}

function set_actions(mode) {


    if (mode == 1) {
        $('input.actions').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', true);
            }
        });
    }

    if (mode == 2) {
        $('input.actions').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', false);
            }
        });
    }
}

function set_hits(mode) {

    if (mode == 1) {
        $('input.hits').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', true);
            }
        });
    }

    if (mode == 2) {
        $('input.hits').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', false);
            }
        });
    }
}

function set_noveltys(mode) {


    if (mode == 1) {
        $('input.noveltys').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', true);
            }
        });
    }

    if (mode == 2) {
        $('input.noveltys').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', false);
            }
        });
    }
}

function set_sales(mode) {


    if (mode == 1) {
        $('input.sales').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', true);
            }
        });
    }

    if (mode == 2) {
        $('input.sales').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', false);
            }
        });
    }
}

function set_specials(mode) {

    if (mode == 1) {
        $('input.specials').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', true);
            }
        });
    }

    if (mode == 2) {
        $('input.specials').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', false);
            }
        });
    }
}

function set_populars(mode) {

    if (mode == 1) {
        $('input.populars').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', true);
            }
        });
    }

    if (mode == 2) {
        $('input.populars').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).prop('checked', false);
            }
        });
    }

}



function deleteLink(id, pid) {

    $('#link_tr' + id).detach();
    var url = "/manage.php?op=ajax.deletelink";
    $.get(
        url,
        {
            link_id: id,
            tovar_id: pid
        }
    );

}

function setLink(id, pid) {
    var plusminus = $('#plusminus' + id);

    var current_sign = plusminus.attr('src');

    var url;

    if (current_sign == '/assets/compo/img/admin/plus.gif') {
        plusminus.attr('src', '/assets/compo/img/admin/minus.gif');
        url = "/manage.php?op=ajax.addlink";
    }
    else {
        plusminus.attr('src', '/assets/compo/img/admin/plus.gif');
        url = "/manage.php?op=ajax.deletelink";
    }
    $.get(
        url,
        {
            link_id: id,
            tovar_id: pid
        }
    );
}

function deleteComplectLink(id, pid) {

    $('#cpl_link_tr' + id).detach();
    var url = "/manage.php?op=ajax.deletecomplectlink";
    $.get(
        url,
        {
            link_id: id,
            tovar_id: pid
        }
    );

}

function setComplectLink(id, pid) {
    var plusminus = $('#plusminus' + id);

    var current_sign = plusminus.attr('src');

    var url;

    if (current_sign == '/assets/compo/img/admin/plus.gif') {
        plusminus.attr('src', '/assets/compo/img/admin/minus.gif');
        url = "/manage.php?op=ajax.addcomplectlink";
    }
    else {
        plusminus.attr('src', '/assets/compo/img/admin/plus.gif');
        url = "/manage.php?op=ajax.deletecomplectlink";
    }
    $.get(
        url,
        {
            link_id: id,
            tovar_id: pid
        }
    );
}

function modifyComplect(checkbox, parent) {
    var url = "/manage.php?op=ajax." + (checkbox.checked == true ? 'addComplect' : 'deleteComplect');
    $.get(
        url,
        {
            id: checkbox.value,
            parent: parent
        }
    );

}

function modifyAdditionalComplect(checkbox, parent) {
    var url = "/manage.php?op=ajax." + (checkbox.checked == true ? 'addAdditionalComplect' : 'deleteAdditionalComplect');
    $.get(
        url,
        {
            id: checkbox.value,
            parent: parent
        }
    );

}

function updateParams() {
    var url = "/manage.php?op=ajax.updateParams&";
    $.get(
        url + $('#form_params').serialize(),
        {
            process: 1

        },
        onParamsSuccess
    );
}

function updatePrices() {

    if ($('#discount').val()) {
        var url = "/manage.php?op=ajax.updatePrices&";
        $.get(
            url + $('#form_prices').serialize(),
            {

                process: 1

            },
            onPricesSuccess
        );
    }

    return false;
}

function updatePricesSuppliers() {

    if ($('#discount').val()) {
        var url = "/manage.php?op=ajax.updatePricesSuppliers&";
        $.get(
            url + $('#form_prices').serialize(),
            {

                process: 1

            },
            onPricesSuccess
        );
    }

    return false;
}

function updateDiscountsSuppliers() {


    if ($('#form_discounts_discount').val()) {
        url = "/manage.php?op=ajax.updateDiscountsSuppliers&";
        $.get(
            url + $('#form_discounts').serialize(),
            {
                process: 1
            },
            onDiscountsSuccess
        );
    }

    return false;
}

function updateDiscountsManufacture() {


    if ($('#form_discounts_discount2').val()) {
        url = "/manage.php?op=ajax.updateDiscountsManufacture&";
        $.get(
            url + $('#form_discounts2').serialize(),
            {
                process: 1
            },
            onDiscountsSuccess2
        );
    }

    return false;
}

function onDiscountsSuccess(data) {
    $('#discounts_success').fadeIn('slow').fadeOut(5000);

}

function onDiscountsSuccess2(data) {
    $('#discounts_success2').fadeIn('slow').fadeOut(5000);

}

function onPricesSuccess() {
    $('#prices_success').fadeIn('slow').fadeOut(5000);

}

function onParamsSuccess() {
    $('#param_success').fadeIn('slow').fadeOut(5000);

}

function is_deleting() {


    var msg = 'Вы действительно желаете удалить эту запись?';

    return window.confirm(msg) == true;


}

function ShowMenu(obj, id) {

    if (cur_menu) {
        cur_menu.style.background = '#000000';
        if (cur_submenu) {
            cur_submenu.style.display = 'none';
            cur_submenu = id;
        }
    }
    cur_menu = obj;
    obj.style.background = '#999999';
    if (id) {
        cur_submenu = document.getElementById('m_' + id);
        cur_submenu.style.display = 'block';
    }

}

function HideMenu() {

    if (cur_menu) {
        cur_menu.style.background = '#000000';
        if (cur_submenu) cur_submenu.style.display = 'none';
    }
    cur_menu = cur_submenu = false;

}

function docStart() {
    var obj = document.getElementById("rSelected");

    if (obj) {
        obj.scrollIntoView(true);
    } else if (document.getElementsByClassName('rSelected')[0] != undefined) {
        document.getElementsByClassName('rSelected')[0].scrollIntoView(true);
    }

}

function set_checkbox(mode) {
    if (mode == 1) {
        $('.checkboxes :checkbox').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $(this).attr('checked', 'checked');
            }
        });
    }

    if (mode == 2) {
        $('.checkboxes input:checked').each(function () {
            if ($(this).attr('disabled') != 'disabled') {
                $('.checkboxes input:checked').removeAttr('checked');
            }
        });
    }
}