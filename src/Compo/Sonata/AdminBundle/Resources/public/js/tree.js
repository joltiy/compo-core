$(function ($) {
    $('.product-catalog-tree li').each(function () {
        var badge = $('.badge-count', this);

        var badge_first = $('.badge-count', this).first();

        var count = 0;

        badge.each(function () {
            if (this.index === 0) {
                return;
            }
            count = count + parseInt($(this).text());
        });

        if (badge.length > 1) {
            badge_first.text(count);
        }
    });


    $('.tree-parent-position, .tree-nested').each(function () {

        var tree_wrap = $(this);

        $('ul.tree-list', $(this)).nestedSortable({
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
            isAllowed: function (placeholder, placeholderParent, currentItem) {
                if (tree_wrap.data('type') === 'nested') {
                    return true;
                }

                return placeholderParent !== undefined;
            },
            stop: function (event, ui) {

                var parent = ui.item.closest('li').parent().closest('li').find('.node-item').first().data('id'),
                    prev = ui.item.prev().children().find('.node-item').data('id'),
                    next = ui.item.next().children().find('.node-item').data('id'),
                    position = false,
                    target = false;
                if (parent && !prev && next) {
                    position = 'before';
                    target = next;
                } else if (prev) {
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

                $.get(ui.item.data('move-url') + '&target=' + target + '&position=' + position, function (data) {
                    if (data.result) {
                        ui.item.css('background', 'white');
                    } else {
                        ui.item.css('background', '#f5c9c9');
                    }
                });


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


        $('a.open-tree', $(this)).each(function () {
            var node_name = 'tree_node.' + $(this).data('alias') + '.' + $(this).data('id');

            if (window.user_settings[node_name] === 'opened') {
                $(this).find('i').removeClass('fa-folder').addClass('fa-folder-open');
                $(this).parent().parent().addClass('opened');
            }
        });

        $('a.node-item', $(this)).click(function () {
            var node_name = 'tree_node.' + $(this).data('alias') + '.' + $(this).data('id');

            var data = {
                'settings': {}
            };

            if ($(this).find('i').hasClass('fa-folder')) {
                data.settings[node_name] = 'opened';

                $(this).find('i').removeClass('fa-folder').addClass('fa-folder-open');
                $(this).parent().parent().addClass('opened');
            } else {
                data.settings[node_name] = 'closed';

                $(this).find('i').removeClass('fa-folder-open').addClass('fa-folder');
                $(this).parent().parent().removeClass('opened');
            }

            $.ajax({
                method: "POST",
                url: Routing.generate('compo_core_update_user_settings'),
                data: data
            });

            return false;
        });
    });

});
