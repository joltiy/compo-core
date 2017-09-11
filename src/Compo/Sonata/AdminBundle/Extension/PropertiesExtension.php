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
class PropertiesExtension extends AbstractAdminExtension
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
        /** @var \Compo\Sonata\AdminBundle\Admin\AbstractAdmin $admin */
        $admin = $formMapper->getAdmin();

        if ($admin->isCurrentRoute('create')) {
            return;
        }

        $formMapper->tab('form.tab_properties')

        ->with('form.group_properties_created', array('name' => false, 'class' => 'col-lg-6'))
        ->add('createdBy', 'sonata_type_model_list',
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
        )
        ->add('createdAt', 'sonata_type_datetime_picker', array(
            'format' => 'dd.MM.y HH:mm:ss',
            'required' => true,
        ), array(
            'translation_domain' => 'SonataAdminBundle'
        ))
        ->end()

        ->with('form.group_properties_updated', array('name' => false, 'class' => 'col-lg-6'))
        ->add('updatedBy', 'sonata_type_model_list',
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
        )
        ->add('updatedAt', 'sonata_type_datetime_picker', array(
            'format' => 'dd.MM.y HH:mm:ss',
            'required' => true,
        ), array(
            'translation_domain' => 'SonataAdminBundle'
        ))
        ->end()

        ->end();
    }

    /**
     * {@inheritDoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {

    }
}