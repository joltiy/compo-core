<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * {@inheritdoc}
 */
class ConfigureTabMenuChildAdminExtension extends AbstractAdminExtension
{
    /**
     * @param AdminInterface      $admin
     * @param MenuItemInterface   $tabMenu
     * @param string              $action
     * @param AdminInterface|null $childAdmin
     */
    public function configureTabMenu(AdminInterface $admin, MenuItemInterface $tabMenu, $action, AdminInterface $childAdmin = null)
    {
        /** @var AbstractAdmin $admin */
        /** @var AbstractAdmin $currentLeafChildAdmin */
        $id = $admin->getRequest()->get('id');

        if (\in_array($action, ['delete', 'edit', 'history', 'untrash'], true)) {
            if ($childAdmin) {
                $currentLeafChildAdmin = $admin->getCurrentLeafChildAdmin();

                /** @var array $children */
                $children = $currentLeafChildAdmin->getChildren();

                /** @var AbstractAdmin $child */
                foreach ($children as $child) {
                    if ($child->hasAccess('list') && $currentLeafChildAdmin->hasChildAdminMenuItem($child->getCode())) {
                        $tabMenu->addChild(
                            'tab_menu.link_list_' . $child->getCode(),
                            [
                                'label' => $childAdmin->getTranslator()->trans('tab_menu.title_list', ['%name%' => $childAdmin->getTranslator()->trans($child->getLabel())]),
                                'uri' => $childAdmin->generateUrl($child->getCode() . '.list', ['id' => $childAdmin->getSubject()->getId()]),
                            ]
                        )->setAttribute('icon', 'fa fa-list');
                    }
                }
            } else {
                /** @var array $children */
                $children = $admin->getChildren();

                /** @var AbstractAdmin $child */
                foreach ($children as $child) {
                    if ($child->hasAccess('list') && $admin->hasChildAdminMenuItem($child->getCode())) {
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
    }
}
