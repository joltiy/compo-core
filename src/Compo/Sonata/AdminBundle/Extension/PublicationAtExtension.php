<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritDoc}
 */
class PublicationAtExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isUseEntityTraits($datagridMapper->getAdmin(), array(
            'Compo\Sonata\AdminBundle\Entity\PublicationAtEntityTrait',
        ) )) {
            return;
        }

        if (!$datagridMapper->has('publicationAt')) {
            $datagridMapper->add('publicationAt');
        }
    }
}