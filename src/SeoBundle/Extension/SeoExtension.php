<?php

namespace Compo\SeoBundle\Extension;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;


class SeoExtension extends AdminExtension
{

    public function alterNewInstance(AdminInterface $admin, $object)
    {

    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

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

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('form.tab_seo')
            ->with('form.tab_seo_main', array('class' => 'col-md-6'))->end()
            ->with('form.tab_seo_meta', array('class' => 'col-md-6'))->end()
            ->end();

        $formMapper
            ->tab('form.tab_seo')
            ->with('form.tab_seo_main')
            ->add('slug', 'text', array('required' => false))
            ->add('header', 'text', array('required' => false))
            ->add('noIndexEnabled', 'checkbox', array('required' => false))
            ->end()
            ->with('form.tab_seo_meta')
            ->add('title', 'text', array('required' => false))
            ->add('metaKeyword', 'text', array('required' => false))
            ->add('metaDescription', 'text', array('required' => false))
            ->end()
            ->end();
    }


    /**
     *
     * {@inheritdoc}
     *
     *
     * @todo Сделать поле slug уникальным, валидация, добавление префикса
     */
    public function preUpdate(AdminInterface $admin, $object)
    {
        /** TODO  */
        if (trim($object->getSlug()) == '') {
            $service = $admin->getConfigurationPool()->getContainer()->get("sonata.core.slugify.cocur");

            $object->setSlug($service->slugify($object->getName()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(AdminInterface $admin, $object)
    {
        if (trim($object->getSlug()) == '') {
            $service = $admin->getConfigurationPool()->getContainer()->get("sonata.core.slugify.cocur");

            $object->setSlug($service->slugify($object->getName()));
        }
    }

}