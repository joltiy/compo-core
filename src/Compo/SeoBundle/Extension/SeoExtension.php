<?php

namespace Compo\SeoBundle\Extension;

use Compo\SeoBundle\Form\SeoVarsType;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritDoc}
 */
class SeoExtension extends AbstractAdminExtension
{
    /**
     * {@inheritDoc}
     */
    public function alterNewInstance(AdminInterface $admin, $object)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('form.tab_seo')
            ->with('form.group_seo_main', array('name' => false, 'class' => 'col-lg-6'))
            ->add('slug', 'text', array('required' => false))
            ->add('noIndexEnabled', 'checkbox', array('required' => false))
            ->end()
            ->with('form.group_seo_meta', array('name' => false, 'class' => 'col-lg-6'))
            ->add('header', 'textarea', array('required' => false, 'attr' => array('class' => 'highlight-src')))
            ->add('title', 'textarea', array('required' => false, 'attr' => array('class' => 'highlight-src')))
            ->add('metaDescription', 'textarea', array('required' => false, 'attr' => array('class' => 'highlight-src')))
            ->add('metaKeyword', 'textarea', array('required' => false, 'attr' => array('class' => 'highlight-src')))
            ->add('seoVars', SeoVarsType::class, array(
                'mapped' => false,
                'required' => false,
                'by_reference' => false,
            ))
            ->end()
            ->end();
    }

}