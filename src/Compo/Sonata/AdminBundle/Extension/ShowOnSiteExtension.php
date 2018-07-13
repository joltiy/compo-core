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
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritdoc}
 */
class ShowOnSiteExtension extends AbstractAdminExtension
{
    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if (!$listMapper->has('_action')) {
            $listMapper->add('_action');
        }

        /** @var AbstractAdmin $admin */
        $admin = $listMapper->getAdmin();

        $_action = $listMapper->get('_action');

        if (null !== $_action) {
            $options = $_action->getOptions();

            if (!isset($options['actions']['show_on_site']) && method_exists($admin, 'generatePermalink')) {
                $options['actions']['show_on_site'] = [
                    'template' => 'SonataAdminBundle:CRUD:list__action_show_on_site.html.twig',
                ];
            }

            $_action->setOptions($options);
        }
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

                if (method_exists($currentLeafChildAdmin, 'generatePermalink') && $currentLeafChildAdmin->generatePermalink($currentLeafChildAdmin->getSubject())) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->getTranslator()->trans('tab_menu.link_show_on_site'),
                        ['uri' => $currentLeafChildAdmin->generatePermalink($currentLeafChildAdmin->getSubject()), 'linkAttributes' => ['target' => '_blank']]
                    )->setAttribute('icon', 'fa fa-eye');
                }
            } elseif (method_exists($admin, 'generatePermalink') && $admin->generatePermalink($admin->getSubject())) {
                $tabMenu->addChild(
                    $admin->trans('tab_menu.link_show_on_site'),
                    ['uri' => $admin->generatePermalink($admin->getSubject()), 'linkAttributes' => ['target' => '_blank']]
                )->setAttribute('icon', 'fa fa-eye');
            }
        }
    }
}
