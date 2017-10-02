<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritDoc}
 */
class NameExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isUseEntityTraits($datagridMapper->getAdmin(), array(
            'Compo\Sonata\AdminBundle\Entity\NameEntityTrait',
        ) )) {
            return;
        }

        if (!$datagridMapper->has('name')) {
            $datagridMapper->add('name');
        }
    }

    public function prePersist(AdminInterface $admin, $object)
    {
        $admin->getModelManager()->getEntityManager($object)->getFilters()->disable("softdeleteable");

    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(AdminInterface $admin, $object)
    {
        $admin->getModelManager()->getEntityManager($object)->getFilters()->enable("softdeleteable");

    }
}