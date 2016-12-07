var fck_instances = [];
var active_storage = false;
var form_contents = [];
var Validators = [];
Validators['email'] = [/^\w+[\+\.\w-]*@([\w-]+\.)*\w+[\w-]*\.([a-z]{2,4}|\d+)$/i, "Введенный e-mail адрес некорректен."];
Validators['color'] = [/^[#]{1}[a-fA-F0-9]{6}$/i, "Укажите цвет в формате #RRGGBB"];
Validators['number'] = [/^[0-9]*$/i, "Пожалуйста введите целое число"];
Validators['float'] = [/^[0-9\.]*$/i, "Пожалуйста введите число"];
Validators['word'] = [/^\S*$/i, "Данное поле не должно содержать пробелы"];
Validators['date'] = [/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/i, "Данное поле должно содержать дату в формате ГГГГ-ММ-ДД"];
Validators['datetime'] = [/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/i, "Данное поле должно содержать дату и время в формате ГГГГ-ММ-ДД ЧЧ:ММ:CC"];

function validate_form(form) {

    var form_name = form.name;
    if (!form_contents[form_name]) {
        form_contents[form_name] = new validator_collection(form);
    }
    form_contents[form_name].validateCollection();
    return (form_contents[form_name].error ? false : true);

}

function validate_collection() {

    this.error = 0;
    for (i = 0; i < this.elements; i++) {
        if (this.items[i].check && this.items[i].validateElement(this.form)) this.error = 1;
    }

}

function validate_element(form) {

    switch (this.type) {
        case "text":
        case "textarea":
            value = this.obj.value;
            if (this.error) {
                this.obj.parentNode.innerHTML = this.html;
                this.obj = form[this.name];
                this.obj.value = value;
                this.error = 0;
            }
            break;
        case "select":
            index = this.obj.selectedIndex;
            value = this.obj.options[index].value;
            if (value == 0) value = '';
            if (this.error) {
                this.obj.parentNode.innerHTML = this.html;
                this.obj = form[this.name];
                this.obj.selectedIndex = index;
                this.error = 0;
            }
            break;
        case "hidden":
            value = this.obj.value;
            if (this.error) {
                this.obj.parentNode.innerHTML = this.html;
                this.obj = form[this.name];
                this.obj.value = value;
                this.error = 0;
            }
            break;
    }
    if (this.maxlength > 0 && value.length > this.maxlength) {
        this.error = 1;
        this.setError(form, 'Максимальная длинна этого поля - ' + this.maxlength + ' символов');
        return true;
    }
    if (value == '') {
        if (this.required) {
            this.error = 1;
            this.setError(form, this.empty);
            return true;
        }
    } else {
        if (this.validate != "std" && !Validators[this.validate][0].test(value)) {
            this.error = 1;
            this.setError(form, Validators[this.validate][1]);
            return true;
        }
    }
    return false;

}

function set_error_element(form, msg) {

    this.obj.parentNode.innerHTML += '<br/><img src="' + image_link + '" width="16" height="16" vspace="3" align="absmiddle" alt="Критическое замечание!"><span class="red">' + msg + '</span>';
    this.obj = form[this.name];

}

function validator_collection(obj) {

    this.error = 0;
    this.form = obj;
    this.items = [];
    for (i = 0; i < obj.elements.length; i++) this.items[i] = new validator_element(obj.elements[i]);
    this.elements = this.items.length;
    this.validateCollection = validate_collection;

}

function validator_element(obj) {

    this.check = 0;
    this.error = 0;
    this.obj = obj;
    this.maxlength = 0;
    this.type = obj.getAttribute("type");
    switch (this.type) {
        case "text":
            this.empty = 'Это поле является обязательным';
            this.name = obj.getAttribute("name");
            this.required = obj.getAttribute("required") && obj.getAttribute("required") == 1 ? 1 : 0;
            this.validate = obj.getAttribute("validate") ? obj.getAttribute("validate") : "std";
            this.html = obj.parentNode.innerHTML;
            if (this.required || this.validate != 'std') this.check = 1;
            break;
        case "select":
            this.empty = 'Пожалуйста выберите значение';
            this.name = obj.getAttribute("name");
            this.validate = 'std';
            this.required = obj.getAttribute("required") && obj.getAttribute("required") == 1 ? 1 : 0;
            if (this.required) this.check = 1;
            this.html = obj.parentNode.innerHTML;
            break;
        case "hidden":
            if (obj.getAttribute("pholder") && obj.getAttribute("pholder") == 1) {
                this.empty = 'Это поле является обязательным';
                this.name = obj.getAttribute("name");
                this.validate = obj.getAttribute("validate") ? obj.getAttribute("validate") : "std";
                this.html = obj.parentNode.innerHTML;
                this.required = 1;
                this.check = 1;
            }
            break;
        case "textarea":
            this.empty = 'Это поле является обязательным';
            this.name = obj.getAttribute("name");
            this.validate = 'std';
            this.required = obj.getAttribute("required") && obj.getAttribute("required") == 1 ? 1 : 0;
            this.maxlength = obj.getAttribute("maxlength") ? obj.getAttribute("maxlength") : 0;
            if (this.required || this.maxlength) this.check = 1;
            this.html = obj.parentNode.innerHTML;
            break;
    }
    this.validateElement = validate_element;
    this.setError = set_error_element;

}

function checkboxDisable(obj, value, mode) {

    var obj_form = obj.form;
    for (i = 0; i < obj_form.elements.length; i++) {
        if (obj_form.elements[i].type != 'checkbox') continue;
        var extracted = obj_form.elements[i].name.substring(obj_form.elements[i].name.length - String(value).length);
        if (mode) {
            if (String(value) != String(extracted)) obj_form.elements[i].checked = false;
        } else {
            if (String(value) == String(extracted)) obj_form.elements[i].checked = false;
        }
    }

}

function constructDate(obj, pfx) {

    var target = obj.form[pfx];
    var source = obj.getAttribute("name");
    if (source == pfx + '_y') {
        if (obj.value * 1 != obj.value || obj.value < 1980) {
            obj.focus();
            obj.select();
            return;
        }
        obj.value = padNum(obj.value, 4);
    } else if (source == pfx + '_j') {
        if (obj.value * 1 != obj.value || obj.value < 1 || obj.value > 12) {
            obj.focus();
            obj.select();
            return;
        }
        obj.value = padNum(obj.value, 2);
    } else if (source == pfx + '_d') {
        if (obj.value * 1 != obj.value || obj.value < 1 || obj.value > 31) {
            obj.focus();
            obj.select();
            return;
        }
        obj.value = padNum(obj.value, 2);
    } else if (source == pfx + '_h') {
        if (obj.value * 1 != obj.value || obj.value > 23) {
            obj.focus();
            obj.select();
            return;
        }
        obj.value = padNum(obj.value, 2);
    } else if (source == pfx + '_m') {
        if (obj.value * 1 != obj.value || obj.value > 59) {
            obj.focus();
            obj.select();
            return;
        }
        obj.value = padNum(obj.value, 2);
    } else if (source == pfx + '_s') {
        if (obj.value * 1 != obj.value || obj.value > 59) {
            obj.focus();
            obj.select();
            return;
        }
        obj.value = padNum(obj.value, 2);
    }
    var value = obj.form[pfx + '_y'].value + '-' + obj.form[pfx + '_j'].value + '-' + obj.form[pfx + '_d'].value;
    if (obj.form[pfx + '_h'] && obj.form[pfx + '_m'] && obj.form[pfx + '_s']) value += ' ' + obj.form[pfx + '_h'].value + ':' + obj.form[pfx + '_m'].value + ':' + obj.form[pfx + '_s'].value;
    target.value = value;

}

function padNum(v, l) {
    while (l > v.length) v = "0" + v;
    return v;
}

function OpenWindow(url, w, h) {
    var sbar = OpenWindow.arguments.length > 3 ? "no" : "yes";
    if (w < 1) w = Math.round(screen.width * w);
    if (h < 1) h = Math.round(screen.height * h);
    wh = window.open(url, "", 'menubar=no,directories=no,location=no,resizable=no,scrollbars=' + sbar + ',width=' + w + ',height=' + h);
    window.onfocus = new Function("if (wh) {try{wh.focus();}catch (e){wh = 0;}}");
}

function FCKeditor_OnComplete(editorInstance) {
    fck_instances[editorInstance.Name] = editorInstance;
}

function initStorage(href, field) {
    if (!fck_instances[field]) return;
    active_storage = field;
    OpenWindow(href, 0.7, 0.7);
}

function catchStorage(code) {
    if (active_storage) fck_instances[active_storage].InsertHtml(code);
}

function injectObj(context, data) {
    switch (context) {
        case "images":
            if (data[0] != 'pic') return;
            window.opener.CustomFotobankAdd('/images/' + data[1] + '/', data[4]);
            window.opener.focus();
            window.close();
            break;
        case "files":
            if (data[0] == 'pic') {
                var link_url = '/images/' + data[1] + '/';
            } else if (data[0] == 'data') {
                var link_url = '/documents/' + data[1] + '/';
            } else {
                return;
            }
            window.opener.CustomLinkerAdd(link_url, data[4]);
            window.opener.focus();
            window.close();
            break;
        case "full":
            var pane = document.getElementById("optPane");
            pane.style.left = Math.round((document.body.clientWidth - 400) / 2);
            pane.style.top = Math.round(document.body.scrollTop + (document.body.clientHeight - 250) / 2);
            if (data[0] == 'pic') {
                var frm = document.getElementById("imgForm");
                frm.obj_id.value = data[1];
                frm.obj_w.value = frm.obj_width.value = data[2];
                frm.obj_h.value = frm.obj_height.value = data[3];
                frm.obj_name.value = data[4];
                frm.obj_resize.checked = frm.obj_preview.checked = frm.obj_wnd.disabled = frm.obj_wnd.checked = frm.obj_link.disabled = false;
                frm.obj_width.disabled = frm.obj_height.disabled = frm.obj_preview.disabled = true;
                frm.obj_bx.value = frm.obj_by.value = 2;
                frm.obj_link.value = '';
                document.getElementById("dataOpt").style.display = 'none';
                document.getElementById("imgOpt").style.display = 'block';
            } else if (data[0] == 'data') {
                var frm = document.getElementById("dataForm");
                frm.obj_id.value = data[1];
                frm.obj_name.value = data[4];
                frm.obj_wnd.checked = false;
                frm.obj_link.value = '/documents/' + data[1] + '/';
                document.getElementById("imgOpt").style.display = 'none';
                document.getElementById("dataOpt").style.display = 'block';
            } else {
                return;
            }
            pane.style.visibility = 'visible';
            document.getElementById("optBorder").style.visibility = 'visible';
            break;
    }

}

function optSet(obj) {
    state = obj.checked ? false : true;
    if (obj.name == 'obj_resize') {
        obj.form.obj_width.disabled = state;
        obj.form.obj_height.disabled = state;
        obj.form.obj_preview.disabled = state;
        obj.form.obj_preview.checked = false;
        obj.form.obj_link.disabled = false;
        obj.form.obj_wnd.disabled = false;
    } else if (obj.name == 'obj_preview') {
        obj.form.obj_link.disabled = obj.checked;
        obj.form.obj_wnd.disabled = obj.checked;
    } else if (obj.name == 'obj_width') {
        if (Math.round(obj.value) == obj.value) {
            obj.form.obj_height.value = Math.round(obj.form.obj_h.value * obj.value / obj.form.obj_w.value);
        } else {
            obj.value = obj.form.obj_w.value;
        }
    } else if (obj.name == 'obj_height') {
        if (Math.round(obj.value) == obj.value) {
            obj.form.obj_width.value = Math.round(obj.form.obj_w.value * obj.value / obj.form.obj_h.value);
        } else {
            obj.value = obj.form.obj_h.value;
        }
    } else if (obj.name == 'obj_bx' || obj.name == 'obj_by') {
        if (Math.round(obj.value) != obj.value) obj.value = 0;
    }

}

function optSave(type) {
    var html_data = "";
    var obj_link = "";
    if (type == 'pic') {
        var frm = document.getElementById("imgForm");
        var align_ptr = frm.obj_align.options[frm.obj_align.selectedIndex].value;
        var border = [Math.round(frm.obj_bx.value) == frm.obj_bx.value ? frm.obj_bx.value : 0, Math.round(frm.obj_by.value) == frm.obj_by.value ? frm.obj_by.value : 0];
        var img_size = [Math.round(frm.obj_height.value) == frm.obj_height.value ? frm.obj_height.value : frm.obj_h.value, Math.round(frm.obj_width.value) == frm.obj_width.value ? frm.obj_width.value : frm.obj_w.value];
        html_data = '<img width="' + img_size[1] + '" hspace="' + border[0] + '" vspace="' + border[1] + '" height="' + img_size[0] + '" title=\'' + frm.obj_name.value + '\' src="/images/' + frm.obj_id.value + '/' + (img_size[0] != frm.obj_h.value ? '/resize' + img_size[1] + '/' : '') + '" border="0"' + (align_ptr ? ' align="' + align_ptr + '"' : '') + '>';
        if (frm.obj_preview.checked) {
            obj_link = "<a href=\"javascript:void(window.open('/viewfoto/" + frm.obj_id.value + "/','','resizable=no,location=yes,menubar=no,scrollbars=no,status=yes,toolbar=no,fullscreen=no,dependent=no,width=" + frm.obj_w.value + ",height=" + frm.obj_h.value + "'));\">";
        } else if (frm.obj_link.value.length) {
            obj_link = '<a href="' + frm.obj_link.value + '"' + (frm.obj_wnd.checked ? ' target="_blank"' : '') + '>';
        }
        if (obj_link.length) html_data = obj_link + html_data + '</a>';
    } else if (type == 'data') {
        var frm = document.getElementById("dataForm");
        html_data = '<a href="' + frm.obj_link.value + '"' + (frm.obj_wnd.checked ? ' target="_blank"' : '') + '>' + (frm.obj_name.value.length ? frm.obj_name.value : 'ссылка') + '</a>';
    } else {
        optCancel();
    }
    window.opener.catchStorage(html_data);
    window.opener.focus();
    window.close();
}

function optCancel() {
    document.getElementById("optBorder").style.visibility = 'hidden';
    document.getElementById("optPane").style.visibility = 'hidden';
}