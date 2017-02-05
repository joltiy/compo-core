<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritDoc}
 */
class EnabledExtension extends AbstractAdminExtension
{


    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if ($listMapper->has('enabled')) {
            $listMapper->get('enabled')->setOption('editable', true);
            $listMapper->get('enabled')->setOption('required', true);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        if ($formMapper->has('enabled')) {
            $formMapper->get('enabled')->setRequired(false);
        }
    }
}