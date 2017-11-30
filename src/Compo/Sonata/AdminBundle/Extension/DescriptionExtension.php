<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritdoc}
 */
class DescriptionExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isUseEntityTraits($datagridMapper->getAdmin(), array(
            'Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait',
        ))) {
            return;
        }

        if (!$datagridMapper->has('description')) {
            $datagridMapper->add('description');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if ($listMapper->has('description')) {
            $keys = $listMapper->keys();

            $listMapper->remove('description');
            $listMapper->add('description', 'html');
            $listMapper->reorder($keys);
        }

        if ($listMapper->has('body')) {
            $keys = $listMapper->keys();

            $listMapper->remove('body');
            $listMapper->add('body', 'html');
            $listMapper->reorder($keys);
        }
    }
}
