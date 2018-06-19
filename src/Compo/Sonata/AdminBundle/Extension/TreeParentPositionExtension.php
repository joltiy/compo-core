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
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * {@inheritdoc}
 */
class TreeParentPositionExtension extends AbstractAdminExtension
{
    /**
     * @param AdminInterface $admin
     *
     * @return array
     */
    public function getAccessMapping(AdminInterface $admin)
    {
        return [
            'tree' => 'LIST',
        ];
    }

    /**
     * @param AdminInterface $admin
     * @param                $object
     */
    public function alterNewInstance(AdminInterface $admin, $object)
    {
        /** @var $admin AbstractAdmin */
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TreeParentPositionEntityTrait',
        ])) {
            return;
        }

        if (!$admin->hasRequest()) {
            return;
        }

        $parentId = $admin->getRequest()->get('parentId');

        if ($parentId) {
            $parent = $admin->getObject($parentId);

            if (method_exists($object, 'setParent')) {
                $object->setParent($parent);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureListMode(AdminInterface $admin)
    {
        /** @var $admin AbstractAdmin */
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TreeParentPositionEntityTrait',
        ])) {
            return;
        }

        $listModes = [
            'tree' => ['class' => 'fa fa-sitemap'],
        ];

        $admin->setListModes(array_merge($listModes, $admin->getListModes()));
    }

    /**
     * Конфигурация шаблонов.
     *
     * @param AdminInterface $admin
     */
    public function configureTemplates(AdminInterface $admin)
    {
        /** @var $admin AbstractAdmin */
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TreeParentPositionEntityTrait',
        ])) {
            return;
        }

        $admin->setTemplate('tree', 'CompoSonataAdminBundle:Tree:tree_parent_position.html.twig');
    }

    /**
     * @param AdminInterface  $admin
     * @param RouteCollection $collection
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TreeParentPositionEntityTrait',
        ])) {
            return;
        }

        $collection->add('move', 'move', ['_controller' => 'CompoSonataAdminBundle:TreeParentPosition:move']);
    }

    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        /** @var AbstractAdmin $admin */
        $admin = $listMapper->getAdmin();

        /** @var $admin AbstractAdmin */
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TreeParentPositionEntityTrait',
        ])) {
            return;
        }

        if (!$listMapper->has('_action')) {
            $listMapper->add('_action');
        }

        $_action = $listMapper->get('_action');

        if (null !== $_action) {
            $options = $_action->getOptions();

            if (!isset($options['actions']['create_parent'])) {
                $options['actions']['create_parent'] = [
                    'template' => 'SonataAdminBundle:CRUD:list__action_create_parent.html.twig',
                ];
            }

            $_action->setOptions($options);
        }
    }
}
