<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * {@inheritdoc}
 */
class PositionExtension extends AbstractAdminExtension
{
    /**
     * {@inheritdoc}
     */
    public function alterNewInstance(AdminInterface $admin, $object)
    {
        if (isset($admin->positionEnabled) && $admin->positionEnabled) {
            /** @var AbstractAdmin $admin */
            $last_position = $admin->getConfigurationPool()->getContainer()->get('pix_sortable_behavior.position')->getLastPosition($admin->getRoot()->getClass());

            $object->setPosition($last_position);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        if (isset($admin->positionEnabled) && $admin->positionEnabled) {
            //$collection->add('move', $admin->getRouterIdParameter() . '/move/{position}');

            $collection->add('sortable', 'sortable', array('_controller' => $admin->getBaseControllerName() . ':sortable'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if (isset($listMapper->getAdmin()->positionEnabled) && $listMapper->getAdmin()->positionEnabled) {
            $_action = $listMapper->get('_action');

            if (null !== $_action) {
                $options = $_action->getOptions();

                //$options['actions']['move'] = array('template' => 'PixSortableBehaviorBundle:Default:_sort.html.twig');
                $options['actions']['sortable'] = array('template' => 'CompoSonataAdminBundle:Button:_sort.html.twig');

                $_action->setOptions($options);
            }
        }
    }
}
