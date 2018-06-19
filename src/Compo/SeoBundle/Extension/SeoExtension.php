<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\SeoBundle\Extension;

use Compo\SeoBundle\Form\SeoVarsType;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class SeoExtension extends AbstractAdminExtension
{
    /**
     * {@inheritdoc}
     */
    public function alterNewInstance(AdminInterface $admin, $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isUseEntityTraits($datagridMapper->getAdmin(), [
            'Compo\SeoBundle\Entity\Traits\SeoEntity',
        ])) {
            return;
        }

        if (!$datagridMapper->has('slug')) {
            $datagridMapper->add('slug');
        }

        if (!$datagridMapper->has('header')) {
            $datagridMapper->add('header');
        }

        if (!$datagridMapper->has('title')) {
            $datagridMapper->add('title');
        }

        if (!$datagridMapper->has('metaDescription')) {
            $datagridMapper->add('metaDescription');
        }

        if (!$datagridMapper->has('metaDescription')) {
            $datagridMapper->add('metaKeyword');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if (!$this->isUseEntityTraits($listMapper->getAdmin(), [
            'Compo\SeoBundle\Entity\Traits\SeoEntity',
        ])) {
            return;
        }

        if (!$listMapper->has('slug')) {
            $listMapper->add('slug');
        }

        if (!$listMapper->has('header')) {
            $listMapper->add('header');
        }

        if (!$listMapper->has('title')) {
            $listMapper->add('title');
        }

        if (!$listMapper->has('metaDescription')) {
            $listMapper->add('metaDescription');
        }

        if (!$listMapper->has('metaDescription')) {
            $listMapper->add('metaKeyword');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->isUseEntityTraits($formMapper->getAdmin(), [
            'Compo\SeoBundle\Entity\Traits\SeoEntity',
        ])) {
            return;
        }

        $formMapper->tab('seo');
        $formMapper->with('seo_main', ['name' => false, 'class' => 'col-lg-6']);

        $formMapper
            ->add('slug', 'text', ['required' => false])
            ->add('noIndexEnabled', 'checkbox', ['required' => false]);

        $formMapper->end();
        $formMapper->with('seo_meta', ['name' => false, 'class' => 'col-lg-6']);

        $formMapper
            ->add('header', 'textarea', ['required' => false, 'attr' => ['class' => 'highlight-src']])
            ->add('title', 'textarea', ['required' => false, 'attr' => ['class' => 'highlight-src']])
            ->add('metaDescription', 'textarea', ['required' => false, 'attr' => ['class' => 'highlight-src']])
            ->add('metaKeyword', 'textarea', ['required' => false, 'attr' => ['class' => 'highlight-src']])
            ->add(
                'seoVars',
                SeoVarsType::class,
                [
                    'mapped' => false,
                    'required' => false,
                    'by_reference' => false,
                ]
            );

        $formMapper->end();
        $formMapper->end();
    }
}
