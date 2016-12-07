$(function ($) {
    $(window).on("backtotopclick", function( event, data ) {
        compo.analytics.reachGoal('compo.backToTop.click', {
            'eventCategory': 'BackToTop',
            'eventAction': 'Click'
        });
    });

    $(window).on("advantagesshowmodal", function( event, data ) {
        compo.analytics.reachGoal('compo.advantages.showModal', {
            'eventCategory': 'Advantages',
            'eventAction': 'ShowModal',
            'recipient': data.recipient
        });
    });

    $(window).on("captchareload", function( event, data ) {
        compo.analytics.reachGoal('compo.captcha.reload', {
            'eventCategory': 'Captcha',
            'eventAction': 'Reload'
        });
    });

    $(window).on('productscatalogblocksliderswipe', function(event, data){
        compo.analytics.reachGoal('compo.productsCatalogBlockSlider.swipe', {
            'eventCategory': 'ProductsCatalogBlockSlider',
            'eventAction': 'Swipe'
        });
    });

    $(window).on('productscatalogblocksliderbeforechange', function(event, data){
        compo.analytics.reachGoal('compo.productsCatalogBlockSlider.change', {
            'eventCategory': 'ProductsCatalogBlockSlider',
            'eventAction': 'Change'
        });
    });

    $(window).on('sidebarcanvasshow', function(event, data){
        compo.analytics.reachGoal('compo.sidebarCanvas.show', {
            'eventCategory': 'Sidebar',
            'eventAction': 'Show'
        });
    });

    $(window).on('panelcollapseshow', function(event, data){
        compo.analytics.reachGoal('compo.panelCollapse.show', {
            'eventCategory': 'PanelCollapse',
            'eventAction': 'Show',
            'name': data.name
        });
    });

    $(window).on('galleryshow', function(event, data){
        compo.analytics.reachGoal('compo.gallery.show', {
            'eventCategory': 'Gallery',
            'eventAction': 'Show'
        });
    });

    $(window).on("compo.order.checkout", function( event, data ) {
        compo.analytics.reachGoal('compo.order.checkout', {
            'eventCategory': 'Ecommerce',
            'eventAction': 'Checkout',
            'ecommerce': data.ecommerce
        });
    });

    $(window).on("compo.order.purchase", function( event, data ) {

        compo.analytics.reachGoal('compo.order.purchase', {
            'eventCategory': 'Ecommerce',
            'eventAction': 'Purchase',
            'ecommerce': data.ecommerce,
            'is_quick': data.is_quick
        });


        if (data.is_quick) {
            compo.analytics.reachGoal('compo.order.purchaseQuick', {
                'is_quick': data.is_quick
            });
        }


    });

    $(window).on("compo.compare.add", function( event, data ) {
        compo.analytics.reachGoal('compo.compare.add', {
            'eventCategory': 'Compare',
            'eventAction': 'Add to compare',
            'ecommerce': {
                'currencyCode': 'RUB',
                'compare': {
                    'actionField': {'list': data.item.list},
                    'products': [{
                        'id':       data.item.id,
                        'name':     data.item.name,
                        'price':    data.item.price,
                        'brand':    data.item.brand,
                        'category': data.item.category,
                        'position': data.item.position
                    }]
                }
            }
        });
    });

    $(window).on("compo.basket.add", function( event, data ) {
        compo.analytics.reachGoal('compo.basket.add', {
            'eventCategory': 'Ecommerce',
            'eventAction': 'Add to cart',
            'ecommerce': {
                'currencyCode': 'RUB',
                'add': {
                    'actionField': {'list': data.item.list},
                    'products': [{
                        'name':     data.item.name,
                        'id':       data.item.id,
                        'price':    data.item.price,
                        'brand':    data.item.brand,
                        'category': data.item.category,
                        'quantity': 1,
                        'position': 1
                    }]
                }
            }
        });
    });


    $(window).on("compo.basket.addSet", function( event, data ) {
        var products = [];

        products.push({
            'name':     data.item.name,
            'id':       data.item.id,
            'price':    data.item.price,
            'brand':    data.item.brand,
            'category': data.item.category,
            'quantity': 1,
            'position': 1
        });

        $.each(data.complects, function( index, value ) {
            products.push({
                'name':     value.name,
                'id':       value.id,
                'price':    value.price,
                'brand':    value.brand,
                'category': value.category,
                'position': value.position,
                'quantity': 1

            });
        });

        compo.analytics.reachGoal('compo.basket.addSet', {
            'eventCategory': 'Ecommerce',
            'eventAction': 'Add to cart set',
            'ecommerce': {
                'currencyCode': 'RUB',
                'add': {
                    'actionField': {'list': data.item.list},
                    'products': products
                }
            }
        });
    });

    $(window).on("compo.productsList.impressions", function( event, data ) {

        compo.analytics.reachGoal('compo.productsList.impressions', {
            'eventCategory': 'Ecommerce',
            'eventAction': 'Impressions products list',
            'ecommerce': {
                'currencyCode': 'RUB',
                'impressions': data.items
            }
        });
    });

    $(window).on("compo.offer.detail", function( event, data ) {
        compo.analytics.reachGoal('compo.offer.detail', {
            'eventCategory': 'Ecommerce',
            'eventAction': 'Product detail',
            'ecommerce': {
                'currencyCode': 'RUB',
                'detail': {
                    'actionField': {'list': 'offer.detail'},
                    'products': [data.item]
                }
            }
        });
    });

    $(window).on("compo.productsFilter.loadProducts", function( event, data ) {
        var hit_data = {
            url: data.url
        };

        compo.analytics.hit(hit_data);
    });

    $(window).on("compo.productsFilter.sorting", function( event, data ) {
        compo.analytics.reachGoal('compo.productsFilter.sorting', data);
    });

    $(window).on("compo.productsFilter.filter", function( event, data ) {
        compo.analytics.reachGoal('compo.productsFilter.filter', data);
    });

    $(window).on("compo.form.submit", function( event, data ) {
        compo.analytics.reachGoal('compo.form.submit', data);
    });

    $(window).on("compo.reviews.submit", function( event, data ) {
        compo.analytics.reachGoal('compo.reviews.submit', data);
    });

    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        compo.analytics.reachGoal('compo.tab.shown', {
            'eventCategory': 'Tab',
            'eventAction': 'Shown',
            'targetId': $(e.target).attr('href').replace(/#/, ''),
            'targetText': $(e.target).text().trim()
        });
    });

    $(document).on('shown.bs.modal', '.modal', function (e) {
        compo.analytics.reachGoal('compo.modal.shown', {
            'eventCategory': 'Modal',
            'eventAction': 'Shown',
            'targetId': $(e.target).attr('id'),
            'targetHeader': $(e.target).find('.modal-header').eq(0).text().trim()
        });
    });

});

