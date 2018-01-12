<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

/**
 * {@inheritdoc}
 */
class ViewsExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isUseEntityTraits($datagridMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\ViewsEntityTrait',
        ])) {
            return;
        }

        if (!$datagridMapper->has('views')) {
            $datagridMapper->add('views');
        }
    }
}
