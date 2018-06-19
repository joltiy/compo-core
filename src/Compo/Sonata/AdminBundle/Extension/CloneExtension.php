<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\SeoBundle\Entity\Traits\SeoEntity;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * {@inheritdoc}
 */
class CloneExtension extends AbstractAdminExtension
{
    /**
     * @param AdminInterface  $admin
     * @param RouteCollection $collection
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        /* @var AbstractAdmin $admin */
        $collection->add('clone', $admin->getRouterIdParameter() . '/clone', ['_controller' => 'CompoSonataAdminBundle:Clone:clone']);
    }

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

            if (
                !isset($options['actions']['clone'])
                &&
                $admin->hasRoute('clone')
                &&
                $admin->hasAccess('create')
            ) {
                $options['actions']['clone'] = [
                    'template' => 'CompoSonataAdminBundle:CRUD:list__action_clone.html.twig',
                ];
            }

            $_action->setOptions($options);
        }
    }

    /**
     * @param AdminInterface $admin
     * @param                $object
     */
    public function preUpdate(AdminInterface $admin, $object)
    {
        /** @var SeoEntity $object */
        if (
            method_exists($object, 'setSlug')
            && method_exists($object, 'getName')
            && false !== mb_strpos($object->getSlug(), 'clone-slug-')
            && false === mb_strpos($object->getName(), '(Copy)')
        ) {
            $object->setSlug(null);
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

                if (!$currentLeafChildAdmin->hasRoute('clone') || !$currentLeafChildAdmin->hasAccess('create')) {
                    return;
                }

                $subject = $currentLeafChildAdmin->getSubject();

                if (!$this->isUseEntityTraits($currentLeafChildAdmin, [
                        'Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity',
                    ]) || !$subject->getDeletedAt()) {
                    $tabMenu->addChild(
                        $currentLeafChildAdmin->getTranslator()->trans('tab_menu.link_clone'),
                        ['uri' => $currentLeafChildAdmin->generateUrl('clone', ['id' => $subject->getId()])]
                    )->setAttribute('icon', 'fa fa-copy');
                }
            } else {
                if (!$admin->hasRoute('clone') || !$admin->hasAccess('create')) {
                    return;
                }

                $subject = $admin->getSubject();

                if (!$this->isUseEntityTraits($admin, [
                        'Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity',
                    ]) || !$subject->getDeletedAt()) {
                    $tabMenu->addChild(
                        $admin->getTranslator()->trans('tab_menu.link_clone'),
                        ['uri' => $admin->generateUrl('clone', ['id' => $subject->getId()])]
                    )->setAttribute('icon', 'fa fa-copy');
                }
            }
        }
    }
}
