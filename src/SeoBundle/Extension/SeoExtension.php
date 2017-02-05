<?php

namespace Compo\SeoBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
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
    public $isUpdateSlug = false;

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
        $this->createSlug($admin, $object);
    }

    public function createSlug(AdminInterface $admin, $object)
    {
        /** @var $admin AbstractAdmin */
        if (trim($object->getSlug()) == '') {
            $service = $admin->getConfigurationPool()->getContainer()->get("sonata.core.slugify.cocur");

            $object->setSlug($service->slugify($object->getName()));
        }

        $qb = $admin->getRepository()->createQueryBuilder('a');
        $qb->where('a.slug = :slug');
        $qb->setParameter('slug', $object->getSlug());

        if ($object->getId()) {
            $qb->andWhere('a.id != :id');
            $qb->setParameter('id', $object->getId());

        }


        $result = $qb->getQuery()->getResult();

        if ($result) {
            $this->isUpdateSlug = true;

            $object->setSlug('temp_slug_' . time() . time() . time());
        } else {
            $this->isUpdateSlug = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(AdminInterface $admin, $object)
    {
        $this->createSlug($admin, $object);
    }

    public function postUpdate(AdminInterface $admin, $object)
    {
        $this->updateSlug($admin, $object);
    }

    public function updateSlug(AdminInterface $admin, $object)
    {
        /** @var $admin AbstractAdmin */

        if (strpos($object->getSlug(), 'temp_slug_') !== false) {
            $service = $admin->getConfigurationPool()->getContainer()->get("sonata.core.slugify.cocur");

            $object->setSlug($service->slugify($object->getName()) . '-' . $object->getId());

            $admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager()->persist($object);
            $admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager()->flush();
        }
    }

    public function postPersist(AdminInterface $admin, $object)
    {
        $this->updateSlug($admin, $object);
    }

}