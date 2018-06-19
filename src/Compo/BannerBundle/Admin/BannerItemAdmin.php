<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\BannerBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class BannerItemAdmin extends AbstractAdmin
{
    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('name')
            ->add('url')
            ->add('enabled')
            ->add(
                '_action'
            );
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->tab(
            'main_banner',
            [
                'translation_domain' => $this->getTranslationDomain(),
            ]
        );

        $formMapper->with(
            'main',
            [
                'name' => false,
            ]
        )
            ->add('id')
            ->add('enabled')
            ->add('banner')
            ->add('name')
            ->add('title')
            ->add('description')

        ;

        $formMapper->add('url');

        $formMapper->add('image');

        $formMapper->end();
        $formMapper->end();
    }
}
