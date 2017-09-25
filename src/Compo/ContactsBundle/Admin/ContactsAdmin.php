<?php

namespace Compo\ContactsBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritDoc}
 */
class ContactsAdmin extends AbstractAdmin
{
    /**
     * {@inheritDoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('list')
            ->remove('delete');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('address')
            ->add('worktime')
            ->add('phone')
            ->add('email')
            ->add('bankprops')
            ->add('walk_instruction')
            ->add('car_instruction')
            ->add('maps_code');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            // ->add('address')
            // ->add('worktime')
            ->add('phone')
            ->add('email')
            // ->add('bankprops')
            // ->add('walk_instruction')
            // ->add('car_instruction')
            //  ->add('maps_code')
            ->add(
                '_action',
                null,
                array(
                    'actions' => array(
                        'show' => array(),
                        'edit' => array(),
                        //  'delete' => array(),
                    )
                )
            );
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('main')
            ->with('major', array('class' => 'col-md-9'))
            ->add('phone', CKEditorType::class)
            ->add('email')
            ->add('worktime', 'ckeditor')
            ->add('address', 'ckeditor')
            ->add('bankprops', 'ckeditor')
            ->end()->end()
            ->tab('instructions')
            ->with('car', array('class' => 'col-md-6'))
            ->add('car_instruction', 'ckeditor', array('required' => false))
            ->end()
            ->with('walk', array('class' => 'col-md-6'))
            ->add('walk_instruction', 'ckeditor', array('required' => false))
            ->end()->end()
            ->tab('map')
            ->with('map', array('class' => 'col-md-9'))
            ->add('maps_code')
            ->add('cix')
            ->add('ciy')
            ->end()
            ->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('address')
            ->add('worktime')
            ->add('phone')
            ->add('email')
            ->add('bankprops')
            ->add('walk_instruction')
            ->add('car_instruction')
            ->add('maps_code');
    }
}
