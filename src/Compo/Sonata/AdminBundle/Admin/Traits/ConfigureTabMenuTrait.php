<?php

namespace Compo\Sonata\AdminBundle\Admin\Traits;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

trait ConfigureTabMenuTrait
{
    /**
     * {@inheritdoc}
     */
    protected function configureTabMenu(MenuItemInterface $tabMenu, $action, AdminInterface $childAdmin = null)
    {
        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        /*
         * список/корзина/создать/показать на сайте
         *
         *      редактирование
         *          редактирование/изменения/клонирование/показать на сайте/товары
         *
         *          товары
         *              список/корзина/создать
         *
         *              редактирование
         *                  редактирование/изменения/клонирование/показать на сайте/отзывы
         *
         *                  отзывы
         *                      список/корзина/создать
         *
         *                      редактирование
         *                              редактирование/изменения/клонирование/показать на сайте
         *
         */

        if (in_array($action, ['list', 'upload', 'import', 'trash', 'tree', 'create'], true)) {
            if ($childAdmin) {
                $currentLeafChildAdmin = $this->getCurrentLeafChildAdmin();

                /*
                dump($this);
                dump($this->getParent());
                dump($childAdmin);
                dump($this->getCurrentChildAdmin());
                dump($this->getCurrentChild());
                dump($this->isChild());
                dump($this->getCurrentLeafChildAdmin());
                dump($this->getChildDepth());
                */

                /*
             {% if admin is defined and action is defined and action == 'list' and admin.listModes|length > 1 %}
                            <div class="nav navbar-right btn-group">
                                {% for mode, settings in admin.listModes %}
                                    <a href="{{ admin.generateUrl('list', app.request.query.all|merge({_list_mode: mode})) }}" class="btn btn-default navbar-btn btn-sm{% if admin.getListMode() == mode %} active{% endif %}"><i class="{{ settings.class }}"></i></a>
                                {% endfor %}
                            </div>
                        {% endif %}
                 */

                if ($currentLeafChildAdmin->hasAccess('list')) {
                    $tabMenuDropdown = $tabMenu->addChild(
                        'tab_menu.list_mode.' . $currentLeafChildAdmin->getLabel(),
                        [
                            'label' => $currentLeafChildAdmin->trans('tab_menu.link_list'),
                            'uri' => $currentLeafChildAdmin->generateUrl('list', []),
                            'attributes' => ['dropdown' => true],
                        ]
                    )->setAttribute('icon', 'fa fa-list');

                    $tabMenuDropdown->addChild(
                        'tab_menu.list_mode.list.' . $currentLeafChildAdmin->getLabel(),
                        [
                            'label' => $currentLeafChildAdmin->trans('tab_menu.link_list'),
                            'uri' => $currentLeafChildAdmin->generateUrl('list', ['_list_mode' => 'list']),
                        ]
                    )->setAttribute('icon', 'fa fa-list');

                    if (isset($currentLeafChildAdmin->treeEnabled) && $currentLeafChildAdmin->treeEnabled) {
                        $tabMenuDropdown->addChild(
                            'tab_menu.list_mode.tree.' . $admin->getLabel(),
                            [
                                'label' => $currentLeafChildAdmin->trans('tab_menu.link_tree'),
                                'uri' => $currentLeafChildAdmin->generateUrl('list', ['_list_mode' => 'tree']),
                            ]
                        )->setAttribute('icon', 'fa fa-sitemap');
                    }
                }

                if ($currentLeafChildAdmin->hasRoute('trash') && $currentLeafChildAdmin->hasAccess('undelete')) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_trash'),
                        ['uri' => $currentLeafChildAdmin->generateUrl('trash', [])]
                    )->setAttribute('icon', 'fa fa-trash');
                }

                if ($currentLeafChildAdmin->hasRoute('create') && $currentLeafChildAdmin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_create'),
                        ['uri' => $currentLeafChildAdmin->generateUrl('create', [])]
                    )->setAttribute('icon', 'fa fa-plus');
                }
            } else {
                if ($admin->hasAccess('list')) {
                    $tabMenuDropdown = $tabMenu->addChild(
                        'tab_menu.list_mode.' . $admin->getLabel(),
                        [
                            'label' => $admin->trans('tab_menu.link_list'),
                            'uri' => $admin->generateUrl('list', []),
                            'attributes' => ['dropdown' => true],
                        ]
                    )->setAttribute('icon', 'fa fa-list');

                    $tabMenuDropdown->addChild(
                        'tab_menu.list_mode.list.' . $admin->getLabel(),
                        [
                            'label' => $admin->trans('tab_menu.link_list'),
                            'uri' => $admin->generateUrl('list', ['_list_mode' => 'list']),
                        ]
                    )->setAttribute('icon', 'fa fa-list');

                    if (isset($admin->treeEnabled) && $admin->treeEnabled) {
                        $tabMenuDropdown->addChild(
                            'tab_menu.list_mode.tree.' . $admin->getLabel(),
                            [
                                'label' => $admin->trans('tab_menu.link_tree'),
                                'uri' => $admin->generateUrl('list', ['_list_mode' => 'tree']),
                            ]
                        )->setAttribute('icon', 'fa fa-sitemap');
                    }
                }

                if ($admin->hasRoute('trash') && $admin->hasAccess('undelete')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_trash'),
                        ['uri' => $admin->generateUrl('trash', [])]
                    )->setAttribute('icon', 'fa fa-trash');
                }

                if ($admin->hasRoute('create') && $admin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $admin->trans('tab_menu.link_create'),
                        ['uri' => $admin->generateUrl('create', [])]
                    )->setAttribute('icon', 'fa fa-plus');
                }
            }
        }

        if (in_array($action, ['list', 'upload', 'import', 'tree'], true)) {
            if ($childAdmin) {
                if (method_exists($childAdmin, 'generatePermalink') && $childAdmin->generatePermalink()) {
                    $tabMenu->addChild(
                        $childAdmin->trans('tab_menu.link_show_on_site'),
                        ['uri' => $childAdmin->generatePermalink(), 'linkAttributes' => ['target' => '_blank']]
                    )->setAttribute('icon', 'fa fa-eye');
                }
            } else {
                if (method_exists($admin, 'generatePermalink') && $admin->generatePermalink()) {
                    $tabMenu->addChild(
                        $admin->trans('tab_menu.link_show_on_site'),
                        ['uri' => $admin->generatePermalink(), 'linkAttributes' => ['target' => '_blank']]
                    )->setAttribute('icon', 'fa fa-eye');
                }
            }
        }

        if (in_array($action, ['delete', 'edit', 'history', 'untrash'], true)) {
            if ($childAdmin) {
                $currentLeafChildAdmin = $this->getCurrentLeafChildAdmin();

                /*
                dump($this);
                dump($this->getParent());
                dump($childAdmin);
                dump($this->getCurrentChildAdmin());
                dump($this->getCurrentChild());
                dump($this->isChild());
                dump($this->getCurrentLeafChildAdmin());
                dump($this->getChildDepth());
                */

                if ($currentLeafChildAdmin->hasAccess('edit', $currentLeafChildAdmin->getSubject())) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_edit'),
                        [
                            'uri' => $currentLeafChildAdmin->generateUrl('edit', ['id' => $currentLeafChildAdmin->getSubject()->getId()]),
                        ]
                    )->setAttribute('icon', 'fa fa-pencil');
                }

                if ($currentLeafChildAdmin->hasRoute('history') && $currentLeafChildAdmin->hasAccess('edit', $currentLeafChildAdmin->getSubject())) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_history'),
                        ['uri' => $currentLeafChildAdmin->generateUrl('history', ['id' => $currentLeafChildAdmin->getSubject()->getId()])]
                    )->setAttribute('icon', 'fa fa-archive');
                }

                if ($currentLeafChildAdmin->hasRoute('clone') && $currentLeafChildAdmin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_clone'),
                        ['uri' => $currentLeafChildAdmin->generateUrl('clone', ['id' => $currentLeafChildAdmin->getSubject()->getId()])]
                    )->setAttribute('icon', 'fa fa-copy');
                }

                if (method_exists($currentLeafChildAdmin, 'generatePermalink') && $currentLeafChildAdmin->generatePermalink($currentLeafChildAdmin->getSubject())) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_show_on_site'),
                        ['uri' => $currentLeafChildAdmin->generatePermalink($currentLeafChildAdmin->getSubject()), 'linkAttributes' => ['target' => '_blank']]
                    )->setAttribute('icon', 'fa fa-eye');
                }

                $children = $currentLeafChildAdmin->getChildren();

                /** @var AdminInterface $child */
                foreach ($children as $child) {
                    if ($child->hasAccess('list')) {
                        $tabMenu->addChild(
                            'tab_menu.link_list_' . $child->getCode(),
                            [
                                'label' => $childAdmin->trans('tab_menu.title_list', ['%name%' => $childAdmin->trans($child->getLabel())]),

                                'uri' => $childAdmin->generateUrl($child->getCode() . '.list', ['id' => $childAdmin->getSubject()->getId()]),
                            ]
                        )->setAttribute('icon', 'fa fa-list');
                    }
                }
            } else {
                if ($admin->hasAccess('edit', $admin->getSubject())) {
                    $tabMenu->addChild(
                        $admin->trans('tab_menu.link_edit'),
                        [
                            'uri' => $admin->generateUrl('edit', ['id' => $id]),
                        ]
                    )->setAttribute('icon', 'fa fa-pencil');
                }

                if ($admin->hasRoute('history') && $admin->hasAccess('edit', $admin->getSubject())) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_history'),
                        ['uri' => $admin->generateUrl('history', ['id' => $admin->getSubject()->getId()])]
                    )->setAttribute('icon', 'fa fa-archive');
                }

                if ($admin->hasRoute('clone') && $admin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $admin->trans('tab_menu.link_clone'),
                        ['uri' => $admin->generateUrl('clone', ['id' => $admin->getSubject()->getId()])]
                    )->setAttribute('icon', 'fa fa-copy');
                }

                if (method_exists($admin, 'generatePermalink') && $admin->generatePermalink($admin->getSubject())) {
                    $tabMenu->addChild(
                        $admin->trans('tab_menu.link_show_on_site'),
                        ['uri' => $admin->generatePermalink($admin->getSubject()), 'linkAttributes' => ['target' => '_blank']]
                    )->setAttribute('icon', 'fa fa-eye');
                }

                $children = $admin->getChildren();

                /** @var AdminInterface $child */
                foreach ($children as $child) {
                    if ($child->hasAccess('list')) {
                        $tabMenu->addChild(
                            'tab_menu.link_list_' . $child->getLabel(),
                            [
                                'label' => $admin->trans('tab_menu.title_list', ['%name%' => $admin->trans($child->getLabel())]),

                                //'uri' => $admin->generateUrl($child->getCode() . '.list', array('id' => $id)),
                                'uri' => $child->generateUrl('list', ['id' => $id]),
                            ]
                        )->setAttribute('icon', 'fa fa-list');
                    }
                }
            }
        }

        return;

        if (!$childAdmin) {
            if ($this->getSubject() && 'create' !== $action) {
                if ($this->hasAccess('edit', $this->getSubject()) && $this->hasRoute('history')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_history'),
                        ['uri' => $this->generateUrl('history', ['id' => $this->getSubject()->getId()])]
                    )->setAttribute('icon', 'fa fa-archive');
                }
            }

            if (
                'edit' !== $action && 'history' !== $action && 'delete' !== $action
            ) {
                if ($this->hasAccess('list')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_list'),
                        ['uri' => $this->generateUrl('list', [])]
                    )->setAttribute('icon', 'fa fa-list');
                }

                if ($this->hasRoute('trash') && $this->hasAccess('undelete')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_trash'),
                        ['uri' => $this->generateUrl('trash', [])]
                    )->setAttribute('icon', 'fa fa-trash');
                }

                if ($this->hasRoute('create') && $this->hasAccess('create')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_create'),
                        ['uri' => $this->generateUrl('create', [])]
                    )->setAttribute('icon', 'fa fa-plus');
                }

                if (method_exists($this, 'generatePermalink') && $this->generatePermalink()) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_show_on_site'),
                        ['uri' => $this->generatePermalink(), 'linkAttributes' => ['target' => '_blank']]
                    )->setAttribute('icon', 'fa fa-eye');
                }
            }

            if ($this->getSubject() && 'create' !== $action) {
                if ($this->hasRoute('clone') && $this->hasAccess('create')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_clone'),
                        ['uri' => $this->generateUrl('clone', ['id' => $this->getSubject()->getId()])]
                    )->setAttribute('icon', 'fa fa-copy');
                }
                $children = $this->getChildren();

                /** @var AdminInterface $child */
                foreach ($children as $child) {
                    if ($child->hasAccess('list')) {
                        $tabMenu->addChild(
                            'tab_menu.link_list_' . $child->getLabel(),
                            [
                                'label' => $this->trans('tab_menu.title_list', ['%name%' => $this->trans($child->getLabel())]),

                                'uri' => $this->generateUrl($child->getBaseCodeRoute() . '.list', ['id' => $this->getSubject()->getId()]),
                            ]
                        )->setAttribute('icon', 'fa fa-list');
                    }

                    /*
                    $tabMenuDropdown = $tabMenu->addChild(
                        'tab_menu.' . $child->getLabel(),
                        array(
                            'label' => $this->trans('tab_menu.title_list', array('%name%' => $this->trans($child->getLabel()))),
                            'attributes' => array('dropdown' => true),
                        )
                    );

                    $tabMenuDropdown->addChild(
                        $this->trans('tab_menu.link_list'),
                        array('uri' => $this->generateUrl($child->getBaseCodeRoute() . '.list', array('id' => $this->getSubject()->getId())))
                    );

                    $tabMenuDropdown->addChild(
                        $this->trans('tab_menu.link_create'),
                        array('uri' => $this->generateUrl($child->getBaseCodeRoute() . '.create', array('id' => $this->getSubject()->getId())))
                    );

                    $tabMenuDropdown->addChild(
                        $this->trans('tab_menu.link_trash'),
                        array('uri' => $this->generateUrl($child->getBaseCodeRoute() . '.trash', array('id' => $this->getSubject()->getId())))
                    );
                    */
                }

                if (method_exists($this, 'generatePermalink') && $this->generatePermalink($this->getSubject())) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_show_on_site'),
                        ['uri' => $this->generatePermalink($this->getSubject()), 'linkAttributes' => ['target' => '_blank']]
                    )->setAttribute('icon', 'fa fa-eye');
                }
            }
        } else {
            if ($this->getSubject() && 'create' !== $action && 'list' !== $action && 'tree' !== $action && 'trash' !== $action && 'untrash' !== $action) {
                if ($childAdmin->hasAccess('edit', $childAdmin->getSubject())) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_edit'),
                        ['uri' => $childAdmin->generateUrl('edit', ['id' => $childAdmin->getSubject()->getId()])]
                    )->setAttribute('icon', 'fa fa-pencil');
                }

                if ($childAdmin->hasRoute('history') && $childAdmin->hasAccess('edit', $childAdmin->getSubject())) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_history'),
                        ['uri' => $childAdmin->generateUrl('history', ['id' => $childAdmin->getSubject()->getId()])]
                    )->setAttribute('icon', 'fa fa-archive');
                }

                if ($childAdmin->hasRoute('clone') && $childAdmin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_clone'),
                        ['uri' => $childAdmin->generateUrl('clone', ['id' => $childAdmin->getSubject()->getId()])]
                    )->setAttribute('icon', 'fa fa-copy');
                }
            }

            if ('create' === $action || 'tree' === $action || 'list' === $action || 'trash' === $action || 'untrash' === $action) {
                if ($childAdmin->hasAccess('list')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_list'),
                        ['uri' => $this->generateUrl($childAdmin->getBaseCodeRoute() . '.list', ['id' => $this->getSubject()->getId()])]
                    )->setAttribute('icon', 'fa fa-list');
                }

                if ($this->hasRoute('create') && $childAdmin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_create'),
                        ['uri' => $this->generateUrl($childAdmin->getBaseCodeRoute() . '.create', ['id' => $this->getSubject()->getId()])]
                    )->setAttribute('icon', 'fa fa-plus');
                }

                if ($childAdmin->hasRoute('trash')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_trash'),
                        ['uri' => $this->generateUrl($childAdmin->getBaseCodeRoute() . '.trash', ['id' => $this->getSubject()->getId()])]
                    )->setAttribute('icon', 'fa fa-trash');
                }
            }

            if ('create' === $action || 'tree' === $action || 'list' === $action || 'trash' === $action || 'untrash' === $action) {
                $admin = $this->getParent();
            } else {
                $admin = $this->getCurrentChildAdmin();
            }

            if ($admin) {
                $children = $admin->getChildren();

                /** @var AdminInterface $child */
                foreach ($children as $child) {
                    if ($child->hasAccess('list')) {
                        $tabMenu->addChild(
                            'tab_menu.link_list_' . $child->getLabel(),
                            [
                                'label' => $this->trans('tab_menu.title_list', ['%name%' => $this->trans($child->getLabel())]),

                                'uri' => $admin->generateUrl($child->getCode() . '.list', ['id' => $admin->getSubject()->getId()]),
                            ]
                        )->setAttribute('icon', 'fa fa-list');
                    }

                    /*
                    $tabMenuDropdown = $tabMenu->addChild(
                        'tab_menu.' . $child->getLabel(),
                        array(
                            'label' => $this->trans('tab_menu.title_list', array('%name%' => $this->trans($child->getLabel()))),
                            'attributes' => array('dropdown' => true),
                        )
                    );

                    $tabMenuDropdown->addChild(
                        $this->trans('tab_menu.link_list'),
                        array('uri' => $this->generateUrl($child->getBaseCodeRoute() . '.list', array('id' => $this->getSubject()->getId())))
                    );

                    $tabMenuDropdown->addChild(
                        $this->trans('tab_menu.link_create'),
                        array('uri' => $this->generateUrl($child->getBaseCodeRoute() . '.create', array('id' => $this->getSubject()->getId())))
                    );

                    $tabMenuDropdown->addChild(
                        $this->trans('tab_menu.link_trash'),
                        array('uri' => $this->generateUrl($child->getBaseCodeRoute() . '.trash', array('id' => $this->getSubject()->getId())))
                    );
                    */
                }
            }
        }
    }
}
