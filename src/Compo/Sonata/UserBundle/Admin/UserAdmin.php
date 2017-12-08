<?php

/*
 * COMPO Форма редактирования пользователя
 */

namespace Compo\Sonata\UserBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

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
            ->with('Profile', array('class' => 'col-md-6'))->end()
            ->with('General', array('class' => 'col-md-6'))->end()
            ->end()
            ->tab('Security')
            ->with('Status', array('class' => 'col-md-6'))->end()
            ->with('Groups', array('class' => 'col-md-6'))->end()
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
                array(
                    'required' => !$this->getSubject() || null === $this->getSubject()->getId(),
                )
            )
            ->end()
            ->with('Profile')
            ->add('firstname', null, array('required' => false))
            ->add('lastname', null, array('required' => false))
            ->add(
                'gender',
                'sonata_user_gender',
                array(
                    'required' => true,
                    'translation_domain' => $this->getTranslationDomain(),
                )
            )
            ->add(
                'dateOfBirth',
                'sonata_type_date_picker',
                array(
                    'format' => 'dd.MM.y',
                    'required' => false,
                )
            )
            //->add('timezone', 'timezone', array('required' => false))
            ->add('phone', null, array('required' => false))
            ->end()
            ->end();

        if ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->tab('Security')
                ->with('Status')
                //->add('locked', null, array('required' => false))
                //->add('expired', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
                //->add('credentialsExpired', null, array('required' => false))
                ->end()
                ->with('Groups')
                ->add(
                    'groups',
                    'sonata_type_model',
                    array(
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true,
                    )
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

        if (in_array($action, array('list', 'trash', 'tree', 'create'))) {
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
                        array(
                            'label' => $currentLeafChildAdmin->trans('tab_menu.link_list'),
                            'uri' => $currentLeafChildAdmin->generateUrl('list', array()),
                            'attributes' => array('dropdown' => true),
                        )
                    )->setAttribute('icon', 'fa fa-list');

                    $tabMenuDropdown->addChild(
                        'tab_menu.list_mode.list.' . $currentLeafChildAdmin->getLabel(),
                        array(
                            'label' => $currentLeafChildAdmin->trans('tab_menu.link_list'),
                            'uri' => $currentLeafChildAdmin->generateUrl('list', array('_list_mode' => 'list')),
                        )
                    )->setAttribute('icon', 'fa fa-list');

                    if (isset($currentLeafChildAdmin->treeEnabled) && $currentLeafChildAdmin->treeEnabled) {
                        $tabMenuDropdown->addChild(
                            'tab_menu.list_mode.tree.' . $admin->getLabel(),
                            array(
                                'label' => $currentLeafChildAdmin->trans('tab_menu.link_tree'),
                                'uri' => $currentLeafChildAdmin->generateUrl('list', array('_list_mode' => 'tree')),
                            )
                        )->setAttribute('icon', 'fa fa-sitemap');
                    }
                }

                if ($currentLeafChildAdmin->hasRoute('trash') && $currentLeafChildAdmin->hasAccess('undelete')) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_trash'),
                        array('uri' => $currentLeafChildAdmin->generateUrl('trash', array()))
                    )->setAttribute('icon', 'fa fa-trash');
                }

                if ($currentLeafChildAdmin->hasRoute('create') && $currentLeafChildAdmin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_create'),
                        array('uri' => $currentLeafChildAdmin->generateUrl('create', array()))
                    )->setAttribute('icon', 'fa fa-plus');
                }
            } else {
                if ($admin->hasAccess('list')) {
                    $tabMenuDropdown = $tabMenu->addChild(
                        'tab_menu.list_mode.' . $admin->getLabel(),
                        array(
                            'label' => $admin->trans('tab_menu.link_list'),
                            'uri' => $admin->generateUrl('list', array()),
                            'attributes' => array('dropdown' => true),
                        )
                    )->setAttribute('icon', 'fa fa-list');

                    $tabMenuDropdown->addChild(
                        'tab_menu.list_mode.list.' . $admin->getLabel(),
                        array(
                            'label' => $admin->trans('tab_menu.link_list'),
                            'uri' => $admin->generateUrl('list', array('_list_mode' => 'list')),
                        )
                    )->setAttribute('icon', 'fa fa-list');

                    if (isset($admin->treeEnabled) && $admin->treeEnabled) {
                        $tabMenuDropdown->addChild(
                            'tab_menu.list_mode.tree.' . $admin->getLabel(),
                            array(
                                'label' => $admin->trans('tab_menu.link_tree'),
                                'uri' => $admin->generateUrl('list', array('_list_mode' => 'tree')),
                            )
                        )->setAttribute('icon', 'fa fa-sitemap');
                    }
                }

                if ($admin->hasRoute('trash') && $admin->hasAccess('undelete')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_trash'),
                        array('uri' => $admin->generateUrl('trash', array()))
                    )->setAttribute('icon', 'fa fa-trash');
                }

                if ($admin->hasRoute('create') && $admin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $admin->trans('tab_menu.link_create'),
                        array('uri' => $admin->generateUrl('create', array()))
                    )->setAttribute('icon', 'fa fa-plus');
                }
            }
        }

        if (in_array($action, array('list', 'tree'))) {
            if ($childAdmin) {
                if (method_exists($childAdmin, 'generatePermalink') && $childAdmin->generatePermalink()) {
                    $tabMenu->addChild(
                        $childAdmin->trans('tab_menu.link_show_on_site'),
                        array('uri' => $childAdmin->generatePermalink(), 'linkAttributes' => array('target' => '_blank'))
                    )->setAttribute('icon', 'fa fa-eye');
                }
            } else {
                if (method_exists($admin, 'generatePermalink') && $admin->generatePermalink()) {
                    $tabMenu->addChild(
                        $admin->trans('tab_menu.link_show_on_site'),
                        array('uri' => $admin->generatePermalink(), 'linkAttributes' => array('target' => '_blank'))
                    )->setAttribute('icon', 'fa fa-eye');
                }
            }
        }

        if (in_array($action, array('delete', 'edit', 'history', 'untrash'))) {
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
                        array(
                            'uri' => $currentLeafChildAdmin->generateUrl('edit', array('id' => $currentLeafChildAdmin->getSubject()->getId())),
                        )
                    )->setAttribute('icon', 'fa fa-pencil');
                }

                if ($currentLeafChildAdmin->hasRoute('history') && $currentLeafChildAdmin->hasAccess('edit', $currentLeafChildAdmin->getSubject())) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_history'),
                        array('uri' => $currentLeafChildAdmin->generateUrl('history', array('id' => $currentLeafChildAdmin->getSubject()->getId())))
                    )->setAttribute('icon', 'fa fa-archive');
                }

                if ($currentLeafChildAdmin->hasRoute('clone') && $currentLeafChildAdmin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_clone'),
                        array('uri' => $currentLeafChildAdmin->generateUrl('clone', array('id' => $currentLeafChildAdmin->getSubject()->getId())))
                    )->setAttribute('icon', 'fa fa-copy');
                }

                if (method_exists($currentLeafChildAdmin, 'generatePermalink') && $currentLeafChildAdmin->generatePermalink($currentLeafChildAdmin->getSubject())) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_show_on_site'),
                        array('uri' => $currentLeafChildAdmin->generatePermalink($currentLeafChildAdmin->getSubject()), 'linkAttributes' => array('target' => '_blank'))
                    )->setAttribute('icon', 'fa fa-eye');
                }

                $children = $currentLeafChildAdmin->getChildren();

                /** @var AdminInterface $child */
                foreach ($children as $child) {
                    if ($child->hasAccess('list')) {
                        $tabMenu->addChild(
                            'tab_menu.link_list_' . $child->getLabel(),
                            array(
                                'label' => $childAdmin->trans('tab_menu.title_list', array('%name%' => $childAdmin->trans($child->getLabel()))),

                                'uri' => $childAdmin->generateUrl($child->getCode() . '.list', array('id' => $childAdmin->getSubject()->getId())),
                            )
                        )->setAttribute('icon', 'fa fa-list');
                    }
                }
            } else {
                if ($admin->hasAccess('edit', $admin->getSubject())) {
                    $tabMenu->addChild(
                        $admin->trans('tab_menu.link_edit'),
                        array(
                            'uri' => $admin->generateUrl('edit', array('id' => $id)),
                        )
                    )->setAttribute('icon', 'fa fa-pencil');
                }

                if ($admin->hasRoute('history') && $admin->hasAccess('edit', $admin->getSubject())) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_history'),
                        array('uri' => $admin->generateUrl('history', array('id' => $admin->getSubject()->getId())))
                    )->setAttribute('icon', 'fa fa-archive');
                }

                if ($admin->hasRoute('clone') && $admin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $admin->trans('tab_menu.link_clone'),
                        array('uri' => $admin->generateUrl('clone', array('id' => $admin->getSubject()->getId())))
                    )->setAttribute('icon', 'fa fa-copy');
                }

                if (method_exists($admin, 'generatePermalink') && $admin->generatePermalink($admin->getSubject())) {
                    $tabMenu->addChild(
                        $admin->trans('tab_menu.link_show_on_site'),
                        array('uri' => $admin->generatePermalink($admin->getSubject()), 'linkAttributes' => array('target' => '_blank'))
                    )->setAttribute('icon', 'fa fa-eye');
                }

                $children = $admin->getChildren();

                /** @var AdminInterface $child */
                foreach ($children as $child) {
                    if ($child->hasAccess('list')) {
                        $tabMenu->addChild(
                            'tab_menu.link_list_' . $child->getLabel(),
                            array(
                                'label' => $admin->trans('tab_menu.title_list', array('%name%' => $admin->trans($child->getLabel()))),

                                'uri' => $admin->generateUrl($child->getCode() . '.list', array('id' => $id)),
                            )
                        )->setAttribute('icon', 'fa fa-list');
                    }
                }
            }
        }

    }

}
