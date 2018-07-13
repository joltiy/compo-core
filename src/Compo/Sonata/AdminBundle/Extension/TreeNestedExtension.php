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
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * {@inheritdoc}
 */
class TreeNestedExtension extends AbstractAdminExtension
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
     * {@inheritdoc}
     */
    public function configureListMode(AdminInterface $admin)
    {
        /** @var $admin AbstractAdmin */
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TreeNestedEntityTrait',
        ])) {
            return;
        }

        $listModes = [
            'tree' => ['class' => 'fa fa-sitemap'],
        ];

        $admin->setListModes(array_merge($listModes, $admin->getListModes()));
    }

    /**
     * @param AdminInterface      $admin
     * @param ProxyQueryInterface $query
     * @param string              $context
     */
    public function configureQuery(AdminInterface $admin, ProxyQueryInterface $query, $context = 'list')
    {
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TreeNestedEntityTrait',
        ])) {
            return;
        }

        /* @var QueryBuilder $query */
        if ('list' === $context) {
            $query->andWhere(
                $query->expr()->gt($query->getRootAliases()[0] . '.lvl', '0')
            );
        }
    }

    /**
     * @param AdminInterface $admin
     * @param array          $filterValues
     */
    public function configureDefaultFilterValues(AdminInterface $admin, array &$filterValues)
    {
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TreeNestedEntityTrait',
        ])) {
            return;
        }

        $datagridValues = [
            '_sort_order' => 'ASC',
            '_sort_by' => 'lft',
        ];

        $filterValues = array_merge(
            $filterValues, $datagridValues
        );
    }

    /**
     * @param AdminInterface $admin
     */
    public function configureTemplates($admin)
    {
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TreeNestedEntityTrait',
        ])) {
            return;
        }

        if ('tree' === $admin->getListMode()) {
            /* @var AbstractAdmin $admin */
            $admin->setTemplate('list', 'CompoSonataAdminBundle:Tree:tree_nested.html.twig');

            $admin->setTemplate('outer_list_rows_tree', 'CompoSonataAdminBundle:CRUD:outer_list_rows_tree.html.twig');
        }
    }

    /**
     * @param AdminInterface $admin
     * @param                $object
     */
    public function alterNewInstance(AdminInterface $admin, $object)
    {
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TreeNestedEntityTrait',
        ])) {
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
     * @param AdminInterface  $admin
     * @param RouteCollection $collection
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TreeNestedEntityTrait',
        ])) {
            return;
        }

        $collection->add('move', 'move', ['_controller' => 'CompoSonataAdminBundle:TreeNested:move']);
    }
}
