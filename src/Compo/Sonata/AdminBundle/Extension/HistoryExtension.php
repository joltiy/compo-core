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
class HistoryExtension extends AbstractAdminExtension
{
    /**
     * @param AdminInterface  $admin
     * @param RouteCollection $collection
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        //$auditManager = $this->getContainer()->get('sonata.admin.audit.manager');

        //if ($auditManager->hasReader($admin->getClass())) {
        //}

        $collection->add('history_revert', $admin->getRouterIdParameter() . '/history/{revision}/revert', ['_controller' => 'CompoSonataAdminBundle:History:historyRevert']);
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

        if (\in_array($action, ['delete', 'edit', 'history', 'untrash'], true)) {
            if ($childAdmin) {
                $currentLeafChildAdmin = $admin->getCurrentLeafChildAdmin();

                $subject = $currentLeafChildAdmin->getSubject();

                if ($currentLeafChildAdmin->hasRoute('history') && $currentLeafChildAdmin->hasAccess('edit', $subject)) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->getTranslator()->trans('tab_menu.link_history'),
                        ['uri' => $currentLeafChildAdmin->generateUrl('history', ['id' => $subject->getId()])]
                    )->setAttribute('icon', 'fa fa-archive');
                }
            } else {
                $subject = $admin->getSubject();

                if ($admin->hasRoute('history') && $admin->hasAccess('edit', $subject)) {
                    $tabMenu->addChild(
                        $admin->getTranslator()->trans('tab_menu.link_history'),
                        ['uri' => $admin->generateUrl('history', ['id' => $subject->getId()])]
                    )->setAttribute('icon', 'fa fa-archive');
                }
            }
        }
    }
}
