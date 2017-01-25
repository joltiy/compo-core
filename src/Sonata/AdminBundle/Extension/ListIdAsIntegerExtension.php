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
        $field = $listMapper->get('id');

        if ($field) {
            $field->setTemplate('SonataIntlBundle:CRUD:list_integer.html.twig');
        }
    }
}