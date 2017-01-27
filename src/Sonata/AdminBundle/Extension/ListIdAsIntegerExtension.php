<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritDoc}
 */
class ListIdAsIntegerExtension extends AbstractAdminExtension
{


    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if ($listMapper->has('id') && $listMapper->get('id')) {
            $listMapper->get('id')->setTemplate('SonataIntlBundle:CRUD:list_integer.html.twig');
        }
    }
}