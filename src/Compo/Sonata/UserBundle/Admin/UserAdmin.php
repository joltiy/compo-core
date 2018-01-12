<?php

/*
 * COMPO Форма редактирования пользователя
 */

namespace Compo\Sonata\UserBundle\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class UserAdmin.
 */
class UserAdmin extends \Sonata\UserBundle\Admin\Model\UserAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        // define group zoning
        $formMapper
            ->tab('User')
            ->with('Profile', ['class' => 'col-md-6'])->end()
            ->with('General', ['class' => 'col-md-6'])->end()
            ->end()
            ->tab('Security')
            ->with('Status', ['class' => 'col-md-6'])->end()
            ->with('Groups', ['class' => 'col-md-6'])->end()
            //->with('Keys', array('class' => 'col-md-4'))->end()
            //->with('Roles', array('class' => 'col-md-12'))->end()
            ->end();

        $formMapper
            ->tab('User')
            ->with('General')
            ->add('username')
            ->add('email')
            ->add(
                'plainPassword',
                'text',
                [
                    'required' => !$this->getSubject() || null === $this->getSubject()->getId(),
                ]
            )
            ->end()
            ->with('Profile')
            ->add('firstname', null, ['required' => false])
            ->add('lastname', null, ['required' => false])
            ->add(
                'gender',
                'sonata_user_gender',
                [
                    'required' => true,
                    'translation_domain' => $this->getTranslationDomain(),
                ]
            )
            ->add(
                'dateOfBirth',
                'sonata_type_date_picker',
                [
                    'format' => 'dd.MM.y',
                    'required' => false,
                ]
            )
            //->add('timezone', 'timezone', array('required' => false))
            ->add('phone', null, ['required' => false])
            ->end()
            ->end();

        if ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->tab('Security')
                ->with('Status')
                //->add('locked', null, array('required' => false))
                //->add('expired', null, array('required' => false))
                ->add('enabled', null, ['required' => false])
                //->add('credentialsExpired', null, array('required' => false))
                ->end()
                ->with('Groups')
                ->add(
                    'groups',
                    'sonata_type_model',
                    [
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true,
                    ]
                )
                ->end()
                /*
                ->with('Roles', array('name' => false))
                ->add('realRoles', 'sonata_security_roles', array(
                    'label' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false,
                ))
                ->end()
                */

                ->end();
        }
    }

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

        if (in_array($action, ['list', 'trash', 'tree', 'create'], true)) {
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

        if (in_array($action, ['list', 'tree'], true)) {
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
                            'tab_menu.link_list_' . $child->getLabel(),
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

                                'uri' => $admin->generateUrl($child->getCode() . '.list', ['id' => $id]),
                            ]
                        )->setAttribute('icon', 'fa fa-list');
                    }
                }
            }
        }
    }
}
