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
class PositionExtension extends AbstractAdminExtension
{
    /**
     * @param AdminInterface $admin
     * @param array          $filterValues
     */
    public function configureDefaultFilterValues(AdminInterface $admin, array &$filterValues)
    {
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\PositionEntityTrait',
        ])) {
            return;
        }

        $datagridValues = [
            '_sort_order' => 'ASC',
            '_sort_by' => 'position',
        ];

        $filterValues = array_merge(
            $filterValues, $datagridValues
        );
    }

    /**
     * @param AdminInterface  $admin
     * @param RouteCollection $collection
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\PositionEntityTrait',
        ])) {
            return;
        }

        $collection->add('sortable', 'sortable', ['_controller' => 'CompoSonataAdminBundle:Position:sortable']);
    }

    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        /** @var AbstractAdmin $admin */
        $admin = $listMapper->getAdmin();

        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\PositionEntityTrait',
        ])) {
            return;
        }

        if (!$listMapper->has('position')) {
            $listMapper->add('position');
        }

        $admin->addDatagridAttribute('data-sortable-action', $admin->generateUrl('sortable'));

        $admin->setDatagridTableClass($admin->getDatagridTableClass() . ' table-sortable');

        $_action = $listMapper->get('_action');

        if (null !== $_action) {
            $filterParameters = $admin->getFilterParameters();

            if (isset($filterParameters['_sort_order'], $filterParameters['_sort_by']) && ('ASC' === $filterParameters['_sort_order'] && 'position' === $filterParameters['_sort_by'])) {
                $options = $_action->getOptions();

                $options['actions']['sortable'] = ['template' => 'CompoSonataAdminBundle:Position:list__action_position.html.twig'];

                $_action->setOptions($options);
            }
        }
    }
}
