<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * {@inheritDoc}
 */
class TabMenuExtension extends AbstractAdminExtension
{
    /**
     * {@inheritDoc}
     */
    public function configureTabMenu(AdminInterface $admin, MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        $menu->setCurrent($admin->getRequest()->getBaseUrl() . $admin->getRequest()->getPathInfo());

        $current = $admin->getRequest()->getBaseUrl() . $admin->getRequest()->getPathInfo();

        foreach ($menu->getChildren() as $child) {


            if ($current == $child->getUri()) {
                $child->setCurrent(true);
            }
        }
    }

}