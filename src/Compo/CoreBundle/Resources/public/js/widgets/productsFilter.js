(function ($) {
    $.widget("compo.productsFilter", {

        isResetProcess: false,
        isSlide: false,
        filterCallback: false,
        isProcessLoad: false,

        currentUrl: '',
        sorting_by: '',
        sorting_order: '',

        isNextPage: false,
        isSorting: false,
        isChangeLocation: false,

        _create: function () {
            var self = this;


            History.Adapter.bind(window, "statechange", function () {
                var state = History.getState();

                self.loadProducts(state.url);
            });

            self.currentUrl = window.location.href;

            self.initSorting();
            self.initPagination();

            self.initChecklists();
            self.initBrands();
            self.initSliders();

            self.initChangePrice();

            $(window).on( "compo.productsFilter.loadProducts", function( event, data ) {
                $.compo.fixHeightRows();

                $('.text-overflow', data.html_wrap).textOverflow();

                data.html_wrap.productsList();

            });

            if (self.window.width() > 767) {
                $('.filter .brands-wrap').height($('.filter .filters-list').height());
            }


            $( window ).resize(function() {
                if (self.window.width() > 767) {
                    $('.filter .brands-wrap').height($('.filter .filters-list').height());
                }
            });

            $(window).on('hidden.bs.collapse', function () {
                if (self.window.width() > 767) {
                    $('.filter .brands-wrap').height($('.filter .filters-list').height());
                }
            });

            $(window).on('shown.bs.collapse', function () {
                if (self.window.width() > 767) {
                    $('.filter .brands-wrap').height($('.filter .filters-list').height());
                }
            });

            $('.filter .btn-search').click(function () {

                self.isChangeLocation = true;

                self.changeFilter($(this), function(data) {
                    window.location.href = self.currentUrl;
                    
                    return false;

                    if (data.filter > 0) {
                        $.smoothScroll({
                            scrollTarget: '#catlist'
                        });
                    }

                });

                return false;
            });


            $('.filter-footer .btn-reset').click(function () {

                self.isResetProcess = true;

                var $btn = $(this).button('loading');

                $(".filter-item .filter-slider").each(function () {
                    var wrap = $(this);

                    var id = wrap.data('id');

                    $("#range_from" + id).val(wrap.data('min'));
                    $("#range_to" + id).val(wrap.data('max'));

                    $("#slider_input_" + id).bootstrapSlider('setValue', [parseInt(wrap.data('min')), parseInt(wrap.data('max'))]);

                    $('.filter-checklist').find('.filter-label').removeClass('active');
                });

                $(".price-slider .filter-slider").each(function () {
                    var wrap = $(this);

                    var id = wrap.data('id');

                    $("#range_from" + id).val(wrap.data('min'));
                    $("#range_to" + id).val(wrap.data('max'));

                    $("#slider_input_" + id).bootstrapSlider('setValue', [parseInt(wrap.data('min')), parseInt(wrap.data('max'))]);

                    $('.filter-checklist').find('.filter-label').removeClass('active');
                });

                $('.filter-checklist').each(function () {
                    $(this).find('input[type="checkbox"]').prop('checked', false);
                    $('.filter-checklist').find('.filter-label').removeClass('active');
                });

                $('.filter-form .brands-wrap').each(function () {
                    $(this).find('input[type="checkbox"]').prop('checked', false);
                });


                $('.filter-label').removeClass('active');


                if (window.location.href == $('.filter-form').attr('action')) {
                    location.reload();

                } else {
                    window.location.href = $('.filter-form').attr('action');

                }

                return false;

                self.sorting_by = '';
                self.sorting_order = '';

                self.changeFilter($(this));

                return false;
            });
        },


        initChecklists: function () {
            var self = this;

            $('.filter-checklist').each(function () {
                var checklist = $(this);

                $(this).find('input[type="checkbox"]').change(function () {

                    var checked = false;

                    checklist.find('input[type="checkbox"]').each(function () {
                        if ($(this).prop('checked')) {
                            checked = true;
                        }
                    });


                    if (checked) {
                        checklist.find('.filter-label').addClass('active');
                    } else {
                        checklist.find('.filter-label').removeClass('active');
                    }

                    self.changeFilter($(this));

                });
            });
        },

        initBrands: function () {
            var self = this;

            $('.filter-form .brands-wrap').each(function () {
                var checklist = $(this);

                $(this).find('input[type="checkbox"].filter-input-brand').change(function () {

                    var el = $(this);
                    
                    var checked = false;

                    checklist.find('input[type="checkbox"].filter-input-brand').each(function () {
                        if ($(this).prop('checked')) {
                            checked = true;
                        }
                    });

                    var wrap = el.parent().parent().parent();

                    var collections = $('.collections', wrap);
                    var collections_list = $('.collections-list', wrap);
                    var collections_list_checked = $('.collections input', wrap);



                    if (checked) {
                        checklist.find('.filter-label').addClass('active');

                        collections.show();

                    } else {
                        checklist.find('.filter-label').removeClass('active');

                        collections.hide();
                        collections_list.hide();
                        collections_list_checked.removeAttr('checked');

                    }

                    self.changeFilter($(this).parent().find('span'));

                });
            });


            $('.brands-wrap .collections .title').click(function () {
                var el = $(this).parent();

                el.find('.collections-list').toggle();

            });


            $('.filter-form .brands-wrap .collections').each(function () {
                var checklist = $(this);

                $(this).find('input[type="checkbox"]').change(function () {

                    var el = $(this);

                    var checked = false;

                    checklist.find('input[type="checkbox"]').each(function () {
                        if ($(this).prop('checked')) {
                            checked = true;
                        }
                    });



                    self.changeFilter($(this));

                });
            });



            
            
        },

        initSliders: function () {
            var self = this;

            $(".filter-slider").each(function () {
                var wrap = $(this);

                var slider = $("#slider_input_" + wrap.data('id'));

                var min = $("#range_from" + wrap.data('id'));
                var max = $("#range_to" + wrap.data('id'));

                var min_val = wrap.data('min');
                var max_val = wrap.data('max');

                var label = wrap.find('.filter-label');

                slider.bootstrapSlider({});

                slider.bootstrapSlider('on', 'slide', function(){
                    self.isSlide = true;
                    min.val(slider.bootstrapSlider('getValue')[0]);
                    max.val(slider.bootstrapSlider('getValue')[1]);
                    self.isSlide = false;
                });


                slider.bootstrapSlider('on', 'change', function(){
                    self.isSlide = true;
                    min.val(slider.bootstrapSlider('getValue')[0]);
                    max.val(slider.bootstrapSlider('getValue')[1]);
                    self.isSlide = false;
                });

                slider.bootstrapSlider('on', 'slideStop', function(){

                    if (self.isProcessLoad == false && self.isSlide == false && self.isResetProcess !== true) {
                        self.changeFilter(label);
                    }

                    if (min.val() != min_val || max.val() != max_val) {
                        label.addClass('active');
                    } else {
                        label.removeClass('active');
                    }
                });

                min.change(function () {
                    if (self.isSlide !== true) {
                        slider.bootstrapSlider('setValue', [parseInt(min.val()), parseInt(max.val())]);

                        if (self.isProcessLoad == false && self.isResetProcess !== true) {
                            self.changeFilter(label);
                        }
                    }
                });

                max.change(function () {
                    if (self.isSlide !== true) {
                        slider.bootstrapSlider('setValue', [parseInt(min.val()), parseInt(max.val())]);

                        if (self.isProcessLoad == false && self.isResetProcess !== true) {
                            self.changeFilter(label);
                        }
                    }
                });
            });
        },


        initPagination: function (){
            var self = this;

            $('.pagination-show-more-block a').click(function(){

                $(this).parent().html('<span class="font-icon glyphicon glyphicon-refresh spinning"></span>');
                self.isNextPage = true;

                self.loadProductsProcess($('.catalog-block-wrap .pagination .active').next().find('a').attr('href'));

                return false;
            });
        },

        initSorting: function () {
            var self = this;

            $('.catalog-sorting-block .method').click(function(){
                self.isSorting = true;

                var el = $(this);

                var url = self.currentUrl;

                url = url.replace(/\/page[0-9]+/, '');

                var class_icon = $(this).find('.font-icon').attr('class');
                var title = '';

                var order = '';

                if (  $(this).data('order') == 'asc' && $(this).hasClass('active') == false) {
                    order = 'asc';
                    title = 'Отсортировано по возрастанию';

                } else if($(this).data('order') == 'desc' && $(this).hasClass('active') == false) {
                    order = 'desc';
                    title = 'Отсортировано по убыванию';

                } else if($(this).data('order') == 'desc' && $(this).hasClass('active') == true) {
                    order = 'asc';
                    title = 'Отсортировано по возрастанию';

                    $(this).data('order', 'asc');
                    class_icon = class_icon.replace('desc', 'asc');

                } else if($(this).data('order') == 'asc' && $(this).hasClass('active') == true) {
                    order = 'desc';
                    title = 'Отсортировано по убыванию';

                    $(this).data('order', 'desc');
                    class_icon = class_icon.replace('asc', 'desc');
                }

                $(window).trigger("compo.productsFilter.sorting", [{
                    'eventCategory': 'Catalog',
                    'eventAction': 'Sorting',
                    order: order,
                    by: $(this).data('by')
                }]);




                $('.catalog-sorting-block .method').removeClass('active');

                $(this).addClass('active');


                $(this).find('.font-icon').attr('class', class_icon);


                if (window.filter_tooltip != undefined) {
                    window.filter_tooltip.tooltip('destroy');
                }

                window.filter_tooltip = $(this);

                clearTimeout(window.search_timeout_btn);

                self.filterCallback = (function(){

                    window.filter_tooltip.tooltip({
                        html: true,
                        placement: 'top',
                        container: 'body',
                        trigger: 'manual',
                        delay: {"show": 500, "hide": 0},
                        title: title
                    });


                    window.filter_tooltip.tooltip('show');

                    window.search_timeout_btn = setTimeout(function () {
                        window.filter_tooltip.tooltip('destroy');
                    }, 3000);

                    self.isSorting = false;

                });

                self.sorting_by = $(this).data('by');
                self.sorting_order = order;

                url = updateQueryStringParameter(url, 'sort_by', $(this).data('by'));
                url = updateQueryStringParameter(url, 'sort_order', order);
                url = updateQueryStringParameter(url, 'page', '');

                self.loadProductsProcess(url);

                return false;
            });
        },



        filterProcess: function (callback) {
            var self = this;

            var args = {};

            $(window).trigger("compo.productsFilter.filter", [{
                'eventCategory': 'Catalog',
                'eventAction': 'Filter'
            }]);



            $(".filter-item .filter-slider").each(function () {
                var wrap = $(this);

                var range_from = $("#range_from" + wrap.data('id')).val();
                var range_to = $("#range_to" + wrap.data('id')).val();

                if (
                    (range_from != wrap.data('min') ||range_to != wrap.data('max'))
                ) {
                    if (args['feature'] == undefined) {
                        args['feature'] = {};
                    }

                    if (args['feature'][wrap.data('id')] == undefined) {
                        args['feature'][wrap.data('id')] = {};

                    }

                    if (range_from != wrap.data('min')) {
                        args['feature'][wrap.data('id')]['from'] = range_from;

                    }

                    if (range_to != wrap.data('max')) {
                        args['feature'][wrap.data('id')]['to'] = range_to;
                    }
                }
            });

            $(".price-slider-item .filter-slider").each(function () {
                var wrap = $(this);

                var range_from = $("#range_from" + wrap.data('id')).val();
                var range_to = $("#range_to" + wrap.data('id')).val();

                if (
                    (range_from != wrap.data('min') ||range_to != wrap.data('max'))
                ) {
                    if (args['price'] == undefined) {
                        args['price'] = {};

                    }

                    if (range_from != wrap.data('min')) {
                        args['price']['from'] = range_from;

                    }

                    if (range_to != wrap.data('max')) {
                        args['price']['to'] = range_to;

                    }
                }
            });

            $('.filter-checklist').each(function () {
                $(this).find('input[type="checkbox"]').each(function () {
                    if ($(this).prop('checked')) {
                        if (args['feature'] == undefined) {
                            args['feature'] = {};

                        }

                        if (args['feature'][$(this).data('feature-id')] == undefined) {
                            args['feature'][$(this).data('feature-id')] = {
                                'items': {}
                            };

                        }

                        args['feature'][$(this).data('feature-id')]['items'][$(this).val()] = $(this).val();


                    }
                });
            });


            $('.filter-form .brands-wrap').each(function () {
                $(this).find('input[type="checkbox"].filter-input-brand').each(function () {
                    if ($(this).prop('checked')) {

                        if (args['manufacture'] == undefined) {
                            args['manufacture'] = {
                                'items': {}
                            };

                        }

                        args['manufacture']['items'][$(this).val()] = $(this).val();

                    }
                });
            });

            $('.filter-form .brands-wrap .collections').each(function () {
                $(this).find('input[type="checkbox"]').each(function () {
                    if ($(this).prop('checked')) {

                        if (args['manufacture_collection'] == undefined) {
                            args['manufacture_collection'] = {
                                'items': {}
                            };


                        }

                        args['manufacture_collection']['items'][$(this).val()] = $(this).val();

                    }
                });
            });




            var action = $('.filter-form').attr('action');

            var parser = document.createElement('a');

            parser.href = action;


            /*
            if (Object.keys(args).length) {
                //args['is_filter'] = 1;

                var params = $.param(args);


            } else {
                var url = parser.protocol + '//' + parser.host + parser.pathname;
            }
            */





            args = sortObject(args);

            var params = $.param(args);


            $('.catalog-filter-block .panel-title').removeClass('active');

            if (params) {
                $('.catalog-filter-block .panel-title').addClass('active');

                $('.filter-footer .btn-reset').addClass('show');
                $('.filter-footer .btn-reset').removeClass('hide');

            } else {
                $('.filter-footer .btn-reset').removeClass('show');
                $('.filter-footer .btn-reset').addClass('hide');

            }

            var url = parser.protocol + '//' + parser.host + parser.pathname + '?' + params;

            url = updateQueryStringParameter(url, 'sort_by', self.sorting_by);
            url = updateQueryStringParameter(url, 'sort_order', self.sorting_order);

            self.filterCallback = callback;

            self.loadProductsProcess(url);
        },

        loadProductsProcess: function (url) {
            var self = this;

            url = url.split('#catlist').join('');


            if (self.isChangeLocation) {
                window.location.href = url + '#catlist';
                return false;
            }
            if (url != self.currentUrl && url != window.location.href && (self.isNextPage || self.isSorting)) {

                History.pushState({}, $('title').text(), url);
            } else {
                self.loadProducts(url);
            }
        },


        loadProducts: function (url, callback) {
            var self = this;

            self.isProcessLoad = true;

            url = updateQueryStringParameter(url, 'sort_by', self.sorting_by);
            url = updateQueryStringParameter(url, 'sort_order', self.sorting_order);

            if (self.xhr != undefined) {
                self.xhr.abort();
            }

            self.xhr = $.get(updateQueryStringParameter(url, 'ajax', 1), function (data) {
                self.isProcessLoad = false;

                self.currentUrl = url;

                var html = $('<div>').append(data.html);

                if (self.isNextPage) {


                    var page = $(data.pagination_wrap).find('.active').first().text().trim();

                    $('.catalog-block-wrap .goods-block').append('' +
                        '<div class="row">' +
                        '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">' +
                        '<div id="pagination_page_'+page+'" class="pagination-page">' +
                        'Страница ' + page +
                        '</div>' +
                        '</div>' +
                        '</div>');

                    $('.catalog-block-wrap .goods-block').append(html);

                    $('.pagination-wrap').html(data.pagination_wrap);

                    self.initPagination();

                    $.smoothScroll({
                        scrollTarget: '#pagination_page_'+page,
                        offset: -20
                    });

                } else {
                    $('.catalog-block-wrap .goods-block').html(html);

                    $('.pagination-wrap').html(data.pagination_wrap);

                    self.initPagination();



                    $('.filter-item').each(function () {
                        var feature_item = $(this);

                        var feature_id = feature_item.data('id');

                        feature_item.removeClass('not-found');

                        if (data.filter_stats.feature.items[feature_id] == undefined) {
                            feature_item.addClass('not-found');
                        }

                        if (feature_item.data('type') == 'variant') {

                            $('.variant-item', feature_item).each(function () {
                                var variant_item = $(this);

                                var variant_id = variant_item.val();

                                variant_item.parent().removeClass('not-found');

                                if (data.filter_stats.feature.items[feature_id] != undefined) {
                                    if (data.filter_stats.feature.items[feature_id]['stats']['items'][variant_id] == undefined) {
                                        variant_item.parent().addClass('not-found');
                                    }
                                } else {
                                    variant_item.parent().addClass('not-found');
                                }
                            });
                        }
                    });

                    $('.brands-wrap .manufacture').removeClass('not-found');

                    $('.filter-input-brand-item').removeClass('not-found');


                    $('.filter-input-brand-item').each(function () {
                        var feature_item = $(this);

                        var feature_id = feature_item.find('.filter-input').val();

                        if (data.filter_stats_manufacture.manufacture.items[feature_id] == undefined) {
                            feature_item.addClass('not-found');
                            feature_item.parent().parent().addClass('not-found');
                        }
                    });


                    $('.brands-wrap .collections label').removeClass('not-found');

                    $('.brands-wrap .collections input').each(function () {
                        var feature_item = $(this);
                        var manufacture_id = $(this).data('manufacture-id');

                        var feature_id = feature_item.val();
                        

                        if (data.filter_stats_manufacture.manufacture.items[manufacture_id] == undefined) {
                            feature_item.parent().addClass('not-found');
                        } else {
                            if (data.filter_stats_manufacture.manufacture.items[manufacture_id]['collections'][feature_id] == undefined) {
                                feature_item.parent().addClass('not-found');
                            }
                        }


                    });

                }

                self.isNextPage = false;

                if (callback != undefined) {
                    callback(data);
                }

                if (self.filterCallback) {
                    self.filterCallback(data);
                }

                self.filterCallback = false;



                data.url = self.currentUrl;

                data.html_wrap = html;

                $(window).trigger("compo.productsFilter.loadProducts", [data]);

            }, 'json');
        },


        changeFilter: function (el, callback) {
            var self = this;

            self.isProcessLoad = true;
            self.isResetProcess = false;

            var placement;

            if (el.hasClass('btn') || el.hasClass('filter-label')) {
                placement = 'top';
            } else {
                placement = 'right';
            }

            if (el.hasClass('filter-input-brand') ) {
                placement = 'right';
            }

            if (el.hasClass('btn')) {
                el.button('loading');
            }

            if (window.filter_tooltip != undefined) {
                window.filter_tooltip.tooltip('destroy');
            }

            if (el.hasClass('filter-input-brand') ) {
                window.filter_tooltip = el.parent().first();
            } else {
                window.filter_tooltip = el.first();

                if (el.hasClass('btn') || el.hasClass('filter-label')) {
                    window.filter_tooltip = el.first();
                } else {
                    window.filter_tooltip = el.parent().find('span').first();
                }
            }

            clearTimeout(window.search_timeout_btn);

            self.filterProcess(function (data) {

                el.button('reset');

                var title = '';

                if (data.filter) {
                    title = $('<a href="' + self.currentUrl + '#catlist" class="link-ajax filter-show">Найдено ' + data.filter + ' из ' + $('.filter-state .total_count').text() + '. Показать.</a>');


                    //title.smoothScroll({offset: -40});

                } else {
                     title = 'Найдено ' + data.filter + ' из ' + data.total ;
                }

                window.filter_tooltip.tooltip({
                    html: true,
                    placement: placement,
                    container: 'body',
                    trigger: 'manual',
                    delay: {"show": 500, "hide": 0},
                    title: title
                });

                $('.filter-state .filtered_count').html(data.filter);
                //$('.filter-state .total_count').html(data.total);

                window.filter_tooltip.tooltip('show');

                window.search_timeout_btn = setTimeout(function () {
                    window.filter_tooltip.tooltip('destroy');
                }, 5000);

                if (callback != undefined) {
                    callback(data);
                }
            });
        },

        initChangePrice: function () {
            var self = this;

            $('.list-links-price a').click(function () {
                var el = $(this);

                var min = $("#range_fromprice");
                var max = $("#range_toprice");

                min.val(parseInt(el.find('.min').eq(0).text().split(" ").join("")));
                max.val(parseInt(el.find('.max').eq(0).text().split(" ").join("")));

                var slider = $("#slider_input_price");

                slider.bootstrapSlider('setValue', [parseInt(min.val()), parseInt(max.val())]);

                var label = slider.parent().parent().find('label');

                self.changeFilter(label);

                return false;
            });
        }
    });


})(jQuery);
