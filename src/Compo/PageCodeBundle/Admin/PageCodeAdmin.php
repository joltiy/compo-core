<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\PageCodeBundle\Admin;

use Compo\PageCodeBundle\Entity\PageCode;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class PageCodeAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('name')
            ->add('layout')
            ->add('enabled')
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
        $formMapper->with('main', ['name' => false, 'class' => 'col-lg-12']);

        $formMapper
            ->add('id')
            ->add('name')
            ->add('enabled')
            ->add(
                'layout',
                'choice',
                [
                    'choices' => $this->getDoctrine()->getRepository(PageCode::class)->getLayoutChoices(),
                ]
            )
            ->add('code');

        $formMapper->end();
        $formMapper->end();
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        $this->updateCacheUpdated();
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        $this->updateCacheUpdated();
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove($object)
    {
        $this->updateCacheUpdated();
    }

    public function updateCacheUpdated()
    {
        $cache = $this->getContainer()->get('cache.app');

        $cache->deleteItem('page_code_updated_at');
    }
}
