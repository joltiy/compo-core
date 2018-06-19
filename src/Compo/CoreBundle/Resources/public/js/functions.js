function units(iNumber, aEndings) {
    if (iNumber == -1) {
        return aEndings[3];
    }
    /*
     var sEnding, i;
     iNumber = iNumber % 100;
     if (iNumber>=11 && iNumber<=19) {
     sEnding=aEndings[2];
     }
     else {
     i = iNumber % 10;
     switch (i)
     {
     case (1): sEnding = aEndings[0]; break;
     case (2):
     case (3):
     case (4): sEnding = aEndings[1]; break;
     default: sEnding = aEndings[2];
     }
     }
     */
    var cases = [2, 0, 1, 1, 1, 2];

    return aEndings[(iNumber % 100 > 4 && iNumber % 100 < 20) ? 2 : cases[(iNumber % 10 < 5) ? iNumber % 10 : 5]];
}

function sortObject(object){
    var sortedObj = {},
        keys = Object.keys(object);

    keys.sort(function(key1, key2){
        key1 = key1.toLowerCase(), key2 = key2.toLowerCase();
        if(key1 < key2) return -1;
        if(key1 > key2) return 1;
        return 0;
    });

    for(var index in keys){
        var key = keys[index];
        if(typeof object[key] == 'object' && !(object[key] instanceof Array)){
            sortedObj[key] = sortObject(object[key]);
        } else {
            sortedObj[key] = object[key];
        }
    }

    return sortedObj;
}

function updateQueryStringParameter(uri, key, value) {

    uri = uri.replace(/#.+$/, '');

    var re = new RegExp("([&])" + key + "=.*?(&|$)", "i");
    var re2 = new RegExp("([?])" + key + "=.*?(&|$)", "i");
    var re3 = new RegExp("([&])" + key + "=.*&", "i");


    var separator = uri.indexOf('?') !== -1 ? "&" : "?";

    var result = '';

    if (uri.match(re3)) {
        if (value != '') {
            result = uri.replace(re3, '$1' + key + "=" + value + '&' );
        } else {
            result = uri.replace(re3, '&');
        }
    } else if (uri.match(re)) {
        if (value != '') {
            result = uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            result = uri.replace(re, '');
        }
    } else if(uri.match(re2)) {
        if (value != '') {
            result = uri.replace(re2, '$1' + key + "=" + value + '$2');
        } else {
            result = uri.replace(re2, '?');
        }
    } else {
        if (value != '') {
            result = uri + separator + key + "=" + value;
        } else {
            result = uri;
        }
    }

    result = result.split("?").join("?");

    if (result.split("?")[1] == undefined || result.split("?")[1] == '') {
        result = result.split("?")[0];
    }


    return result;
}

function number_format(number, decimals, dec_point, thousands_sep) {
    // Format a number with grouped thousands
    //
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +	 bugfix by: Michael White (http://crestidg.com)

    var i, j, kw, kd, km;

    // input sanitation & defaults
    if (isNaN(decimals = Math.abs(decimals))) {
        decimals = 2;
    }
    if (dec_point === undefined) {
        dec_point = ",";
    }
    if (thousands_sep === undefined) {
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


