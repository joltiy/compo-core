(function () {
    'use strict';

    angular
        .module('Directives')
        .directive('ngPhoneMask', [ngPhoneMask]);

    /* @ngInject */
    function ngPhoneMask() {
        return {
            link: function (scope, elem) {
                var getCaretPosition = function (ctrl) {
                    // IE < 9 Support
                    if (document.selection) {
                        ctrl.focus();
                        var range = document.selection.createRange();
                        var rangelen = range.text.length;
                        range.moveStart ('character', -ctrl.value.length);
                        var start = range.text.length - rangelen;
                        return {'start': start, 'end': start + rangelen };
                    }
                    // IE >=9 and other browsers
                    else if (ctrl.selectionStart || ctrl.selectionStart == '0') {
                        return {'start': ctrl.selectionStart, 'end': ctrl.selectionEnd };
                    } else {
                        return {'start': 0, 'end': 0};
                    }
                };

                var setCaretPosition = function (ctrl, start, end) {
                    // IE >= 9 and other browsers
                    if(ctrl.setSelectionRange)
                    {
                        ctrl.focus();
                        ctrl.setSelectionRange(start, end);
                    }
                    // IE < 9
                    else if (ctrl.createTextRange) {
                        var range = ctrl.createTextRange();
                        range.collapse(true);
                        range.moveEnd('character', end);
                        range.moveStart('character', start);
                        range.select();
                    }
                };

                var phoneCaret = function () {
                    var el = $(this);
                    var elDom = el.get(0);

                    var phone = $(this).val();

                    var phoneArray = phone.split('');

                    var pos = 0;
                    var stopPos = false;

                    $.each(phoneArray, function( index, value ) {

                        if (stopPos === true) {
                            return;
                        }

                        if (value === '+') {
                            pos++;
                            return;
                        }

                        if ($.isNumeric(value)) {
                            pos++;
                            return;
                        }

                        if (value === '(') {
                            pos++;
                            return;
                        }
                        if (value === ')') {
                            pos++;
                            return;
                        }

                        if (value === '-') {
                            pos++;
                            return;
                        }

                        stopPos = true;
                    });


                    if (getCaretPosition(elDom).start > pos) {
                        setCaretPosition(elDom, pos, pos);
                    }
                };

                elem.click(phoneCaret);
                elem.focus(phoneCaret);
                elem.select(phoneCaret);

                elem.mask("+7(999)999-99-99", {autoclear: false});
            }
        }
    }

})();
