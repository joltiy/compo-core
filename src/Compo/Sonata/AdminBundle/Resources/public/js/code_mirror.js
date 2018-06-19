$(function ($) {
    CodeMirror.defineMode("html_twig", function (config, parserConfig) {
        return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || "text/html"), CodeMirror.getMode(config, "twig"));
    });

    $(window).on("compo.sonata.admin.shared_setup", function( event, data ) {

        Admin.log('[compo|CodeMirror] on', data.subject);

        $(data.subject).on('shown.bs.tab', 'a[data-toggle="tab"]', function () {
            $('.CodeMirror', data.subject).each(function (i, el) {
                el.CodeMirror.refresh();
            });
        });

        $('.highlight-src', data.subject).each(function () {
            var el = $(this);

            var myCodeMirror = CodeMirror.fromTextArea(el.get(0), {
                lineNumbers: true,
                mode: "html_twig",
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

    });
});
