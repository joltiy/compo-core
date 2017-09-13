<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritDoc}
 */
class BlameableExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {


    }

    /**
     * {@inheritDoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $this->replaceFormField(
            $formMapper,
            'createdBy',
            'sonata_type_model_list',
            array(
                'required' => false
            ),
            array(
                'link_parameters' => array(
                    'context' => 'default',
                    'hide_context' => true,
                ),
                'translation_domain' => 'SonataAdminBundle'
            )
        );

        $this->replaceFormField(
            $formMapper,
            'updatedBy',
            'sonata_type_model_list',
            array(
                'required' => false
            ),
            array(
                'link_parameters' => array(
                    'context' => 'default',
                    'hide_context' => true,
                ),
                'translation_domain' => 'SonataAdminBundle'
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {

    }
}