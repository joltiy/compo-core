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
class ConfigureTabMenuExtension extends AbstractAdminExtension
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

        if (\in_array($action, ['list', 'upload', 'import', 'trash', 'tree'], true)) {
            if ($childAdmin) {
                $currentLeafChildAdmin = $admin->getCurrentLeafChildAdmin();

                $listModes = $currentLeafChildAdmin->getListModes();

                if ($currentLeafChildAdmin->hasAccess('list') && \count($listModes)) {
                    $currentListMode = $admin->getListMode();

                    $tabMenuDropdown = $tabMenu->addChild(
                        'tab_menu.list_mode.' . $currentListMode . '.' . $currentLeafChildAdmin->getLabel(),
                        [
                            'label' => $currentLeafChildAdmin->trans('tab_menu.link_' . $currentListMode),
                            'uri' => $currentLeafChildAdmin->generateUrl('list'),
                        ]
                    )->setAttribute('icon', $listModes[$currentListMode]['class']);

                    if (\count($listModes) > 1) {
                        $tabMenuDropdown->setAttribute('dropdown', true);

                        foreach ($listModes as $mode => $modeConfig) {
                            $tabMenuDropdown->addChild(
                                'tab_menu.list_mode.' . $mode . '.' . $currentLeafChildAdmin->getLabel(),
                                [
                                    'label' => $currentLeafChildAdmin->trans('tab_menu.link_' . $mode),
                                    'uri' => $currentLeafChildAdmin->generateUrl('list', ['_list_mode' => $mode]),
                                ]
                            )->setAttribute('icon', $listModes[$mode]['class']);
                        }
                    }
                }

                if ($currentLeafChildAdmin->hasRoute('create') && $currentLeafChildAdmin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->trans('tab_menu.link_create'),
                        ['uri' => $currentLeafChildAdmin->generateUrl('create', [])]
                    )->setAttribute('icon', 'fa fa-plus');
                }
            } else {
                $listModes = $admin->getListModes();

                if ($admin->hasAccess('list') && \count($listModes)) {
                    $currentListMode = $admin->getListMode();

                    $tabMenuDropdown = $tabMenu->addChild(
                        'tab_menu.list_mode.' . $currentListMode . '.' . $admin->getLabel(),
                        [
                            'label' => $admin->trans('tab_menu.link_' . $currentListMode),
                            'uri' => $admin->generateUrl('list'),
                        ]
                    )->setAttribute('icon', $listModes[$currentListMode]['class']);

                    if (\count($listModes) > 1) {
                        $tabMenuDropdown->setAttribute('dropdown', true);

                        foreach ($listModes as $mode => $modeConfig) {
                            $tabMenuDropdown->addChild(
                                'tab_menu.list_mode.' . $mode . '.' . $admin->getLabel(),
                                [
                                    'label' => $admin->trans('tab_menu.link_' . $mode),
                                    'uri' => $admin->generateUrl('list', ['_list_mode' => $mode]),
                                ]
                            )->setAttribute('icon', $listModes[$mode]['class']);
                        }
                    }
                }

                if ($admin->hasRoute('create') && $admin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $admin->getTranslator()->trans('tab_menu.link_create'),
                        ['uri' => $admin->generateUrl('create', [])]
                    )->setAttribute('icon', 'fa fa-plus');
                }
            }
        }

        if (\in_array($action, ['delete', 'edit', 'history', 'untrash'], true)) {
            if ($childAdmin) {
                $currentLeafChildAdmin = $admin->getCurrentLeafChildAdmin();

                $subject = $currentLeafChildAdmin->getSubject();

                if ($currentLeafChildAdmin->hasAccess('edit', $subject)) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->getTranslator()->trans('tab_menu.link_edit'),
                        [
                            'uri' => $currentLeafChildAdmin->generateUrl('edit', ['id' => $subject->getId()]),
                        ]
                    )->setAttribute('icon', 'fa fa-pencil');
                }
            } elseif ($admin->hasAccess('edit', $admin->getSubject())) {
                $tabMenu->addChild(
                    $admin->trans('tab_menu.link_edit'),
                    [
                        'uri' => $admin->generateUrl('edit', ['id' => $id]),
                    ]
                )->setAttribute('icon', 'fa fa-pencil');
            }
        }
    }
}
