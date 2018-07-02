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
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * {@inheritdoc}
 */
class TrashExtension extends AbstractAdminExtension
{
    /**
     * @param AdminInterface  $admin
     * @param RouteCollection $collection
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        $collection->add('trash', 'trash', ['_controller' => 'CompoSonataAdminBundle:Trash:trash']);
        $collection->add('untrash', $admin->getRouterIdParameter() . '/untrash', ['_controller' => 'CompoSonataAdminBundle:Trash:untrash']);
    }

    /**
     * @param AdminInterface $admin
     *
     * @return array
     */
    public function getAccessMapping(AdminInterface $admin)
    {
        return [
            'trash' => 'UNDELETE',
            'undelete' => 'UNDELETE',
        ];
    }

    /**
     * @param AdminInterface      $admin
     * @param MenuItemInterface   $tabMenu
     * @param string              $action
     * @param AdminInterface|null $childAdmin
     */
    public function configureTabMenu(AdminInterface $admin, MenuItemInterface $tabMenu, $action, AdminInterface $childAdmin = null)
    {
        /* @var  AbstractAdmin $admin */
        /* @var  AbstractAdmin $currentLeafChildAdmin */

        if (\in_array($action, ['list', 'upload', 'import', 'trash', 'tree', 'create'], true)) {
            if ($childAdmin) {
                $currentLeafChildAdmin = $admin->getCurrentLeafChildAdmin();

                if ($currentLeafChildAdmin->hasRoute('trash') && $currentLeafChildAdmin->hasAccess('undelete')) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_trash'),
                        ['uri' => $currentLeafChildAdmin->generateUrl('trash', [])]
                    )->setAttribute('icon', 'fa fa-trash');
                }
            } elseif ($admin->hasRoute('trash') && $admin->hasAccess('undelete')) {
                $tabMenu->addChild(
                    $admin->getTranslator()->trans('tab_menu.link_trash'),
                    ['uri' => $admin->generateUrl('trash', [])]
                )->setAttribute('icon', 'fa fa-trash');
            }
        }
    }
}
