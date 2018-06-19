<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ContactsBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

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
            ->add('bankProps')
            ->add('walkInstruction')
            ->add('carInstruction')
            ->add('mapsCode');
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
            // ->add('bankProps')
            // ->add('walkInstruction')
            // ->add('carInstruction')
            //  ->add('mapsCode')
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
        $formMapper->tab('main');
        $formMapper->with('major', ['class' => 'col-md-9']);

        $formMapper
            ->add('name')
            ->add('phone', CKEditorType::class)
            ->add('email')
            ->add('worktime', CKEditorType::class)
            ->add('address', CKEditorType::class)
            ->add('bankProps', CKEditorType::class, ['required' => false])
        ;

        $formMapper->end();
        $formMapper->end();

        $formMapper->tab('instructions');
        $formMapper->with('car', ['class' => 'col-md-6']);

        $formMapper->add('carInstruction', CKEditorType::class, ['required' => false]);

        $formMapper->end();
        $formMapper->with('walk', ['class' => 'col-md-6']);

        $formMapper->add('walkInstruction', CKEditorType::class, ['required' => false]);

        $formMapper->end();
        $formMapper->end();

        $formMapper->tab('map');
        $formMapper->with('map', ['class' => 'col-md-9']);

        $formMapper
            ->add('mapsCode', 'textarea', ['required' => false, 'attr' => ['class' => 'highlight-src']])
            ->add('cix')
            ->add('ciy')
        ;

        $formMapper->end();
        $formMapper->end();
    }
}
