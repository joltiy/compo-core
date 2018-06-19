<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\AdvantagesBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class AdvantagesItemAdmin extends AbstractAdmin
{
    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('name')
            ->add('url', 'url', ['attributes' => ['target' => '_blank']])
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
            ->add('createdBy')
            ->add('updatedBy')
            ->add('enabled')
            ->add('position')
            ->add('_action');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->tab('main');

        $formMapper->with(
            'main',
            [
                'name' => false,
            ]
        );

        $formMapper->add('id');
        $formMapper->add('enabled');
        $formMapper->add('advantages', null, ['required' => true]);
        $formMapper->add('name');
        $formMapper->add('title');
        $formMapper->add('description', CKEditorType::class, ['required' => false]);
        $formMapper->add('url');
        $formMapper->add('image');

        $formMapper->end();
        $formMapper->end();
    }
}
