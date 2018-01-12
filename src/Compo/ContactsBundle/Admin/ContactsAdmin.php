<?php

namespace Compo\ContactsBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritdoc}
 */
class ContactsAdmin extends AbstractAdmin
{
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
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->add('address', 'html')
            // ->add('worktime')
            ->add('phone', 'html')
            ->add('email')
            // ->add('bankprops')
            // ->add('walk_instruction')
            // ->add('car_instruction')
            //  ->add('maps_code')
            ->add(
                '_action',
                null,
                [
                ]
            );
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('main')
            ->with('major', ['class' => 'col-md-9'])
            ->add('name')

            ->add('phone', CKEditorType::class)
            ->add('email')
            ->add('worktime', 'ckeditor')
            ->add('address', 'ckeditor')
            ->add('bankprops', 'ckeditor', ['required' => false])
            ->end()->end()
            ->tab('instructions')
            ->with('car', ['class' => 'col-md-6'])
            ->add('car_instruction', 'ckeditor', ['required' => false])
            ->end()
            ->with('walk', ['class' => 'col-md-6'])
            ->add('walk_instruction', 'ckeditor', ['required' => false])
            ->end()->end()
            ->tab('map')
            ->with('map', ['class' => 'col-md-9'])
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
