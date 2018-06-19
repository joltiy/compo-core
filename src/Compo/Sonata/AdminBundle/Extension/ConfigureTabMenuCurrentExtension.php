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
class ConfigureTabMenuCurrentExtension extends AbstractAdminExtension
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
        $request = $admin->getRequest();

        $baseUrl = $request->getBaseUrl();
        $pathInfo = $request->getPathInfo();

        $currentPath = $baseUrl . $pathInfo;

        $current = $request->getRequestUri();

        $tabMenu->setCurrent($current);

        foreach ($tabMenu->getChildren() as $child) {
            $childUri = $child->getUri();

            if ($current === $childUri || $childUri === $currentPath || $childUri === $request->getUri()) {
                $child->setCurrent(true);
            }
        }
    }
}
