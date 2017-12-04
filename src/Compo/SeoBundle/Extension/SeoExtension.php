<?php

namespace Compo\SeoBundle\Extension;

use Compo\SeoBundle\Form\SeoVarsType;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

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
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->isUseEntityTraits($formMapper->getAdmin(), array(
            'Compo\SeoBundle\Entity\Traits\SeoEntity',
        ))) {
            return;
        }

        $formMapper
            ->tab('seo')
            ->with('seo_main', array('name' => false, 'class' => 'col-lg-6'))
            ->add('slug', 'text', array('required' => false))
            ->add('noIndexEnabled', 'checkbox', array('required' => false))
            ->end()
            ->with('seo_meta', array('name' => false, 'class' => 'col-lg-6'))
            ->add('header', 'textarea', array('required' => false, 'attr' => array('class' => 'highlight-src')))
            ->add('title', 'textarea', array('required' => false, 'attr' => array('class' => 'highlight-src')))
            ->add('metaDescription', 'textarea', array('required' => false, 'attr' => array('class' => 'highlight-src')))
            ->add('metaKeyword', 'textarea', array('required' => false, 'attr' => array('class' => 'highlight-src')))
            ->add(
                'seoVars',
                SeoVarsType::class,
                array(
                    'mapped' => false,
                    'required' => false,
                    'by_reference' => false,
                )
            )
            ->end()
            ->end();
    }
}
