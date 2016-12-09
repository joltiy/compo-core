var compo = {};

window.images_base_path = 'http://www.dlyavann.ru';

$(function ($) {
    $.extend($.validator.messages, {
        pattern: "Неверный формат"
    });


    compo.analytics  = $.compo.analytics({
        yandexMetrikaId: window.yandex_metrika_id || '',
        userId: window.userId || null,
        ip: window.ip
    });

    compo.productsFilter = $.compo.productsFilter();

    compo.productsCompare = $.compo.productsCompare();

    compo.offer = $.compo.offer();

    compo.basket = $.compo.basket();

    compo.order = $.compo.order();
    
    compo.user = $.compo.user();








    compo.orderNavigation = $.compo.orderNavigation();

    $.compo.reviews();

    $.compo.popoverFix();
    $.compo.imagesReplace();
    //$.compo.backToTop();
    $.compo.sidebarCanvas();
    $.compo.menuBrandsList();
    $.compo.fixHeightRows();




    $('[itemtype="http://schema.org/ItemList"]').productsList();

    $('#advantages-modal').advantages();

    $('.captcha').captcha();

    $('.text-overflow').textOverflow();

    $('.phone-input').phoneInput();

    $('[data-toggle="tooltip"]').tooltip();

    $('.panel-collapse-panel .collapse').panelCollapse();

    $('.compare-wrap .list-products').productsCatalogBlockSlider();
    $('.specials .catalog-block-items-list').productsCatalogBlockSlider();
    $('.last-viewed-products-bottom .catalog-block-items-list').productsCatalogBlockSlider();
    $('.allcollection .catalog-block-items-list').productsCatalogBlockSlider();
    $('.incollection .catalog-block-items-list').productsCatalogBlockSlider();

    $('.body-sostav .catalog-block-items-list').productsCatalogBlockSlider();


    $('#header').find('.search input[name="keyword"]').productsSearch();
    $('.page-search #content .search  .form-control').productsSearch();


    $(".box-img-prod ul").gallery();
    $(".page-about .slider").gallery();

    
    $('.fancybox-iframe').click(onClickFancyboxFrame);

    $('.fancybox-ajax').click(onClickFancyboxAjax);


    $(".show-more-button .link").click(function(){
        var link = $(this);

        var complete = link.closest('.show-more-block').find(".show-more-complete");

        if (complete.is(':visible')) {
            link.text("Подробнее...");

            complete.hide("slow");
        } else {
            link.text("Скрыть");

            complete.show("slow");
        }
    });

});

