<?php

namespace Compo\SeoBundle\Extension;

use Compo\SeoBundle\Entity\Traits\SeoEntity;
use Compo\SeoBundle\Form\SeoVarsType;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Doctrine\ORM\EntityRepository;
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
            ->with('form.group_seo_main', array('name' => false, 'class' => 'col-lg-6'))
            ->add('slug', 'text', array('required' => false))
            ->add('noIndexEnabled', 'checkbox', array('required' => false))
            ->end()
            ->with('form.group_seo_meta', array('name' => false, 'class' => 'col-lg-6'))
            ->add('header', 'textarea', array('required' => false))
            ->add('title', 'textarea', array('required' => false))
            ->add('metaDescription', 'textarea', array('required' => false))
            ->add('metaKeyword', 'textarea', array('required' => false))
            ->add('seoVars', SeoVarsType::class, array(
                'mapped' => false,
                'required' => false,
                'by_reference' => false,
            ))
            ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(AdminInterface $admin, $object)
    {
        $this->createSlug($admin, $object);
    }

    public function createSlug(AdminInterface $admin, $object)
    {
        /** @var $admin AbstractAdmin */
        /** @var SeoEntity $object */
        if (trim($object->getSlug()) == '') {
            $service = $admin->getConfigurationPool()->getContainer()->get("sonata.core.slugify.cocur");

            /** @noinspection PhpUndefinedMethodInspection */
            $object->setSlug($service->slugify($object->getName()));
        }

        /** @var EntityRepository $repository */
        $repository = $admin->getRepository();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $repository->createQueryBuilder('a');
        $qb->where('a.slug = :slug');
        $qb->setParameter('slug', $object->getSlug());

        /** @noinspection PhpUndefinedMethodInspection */
        if ($object->getId()) {
            $qb->andWhere('a.id != :id');
            /** @noinspection PhpUndefinedMethodInspection */
            $qb->setParameter('id', $object->getId());
        }

        $result = $qb->getQuery()->getResult();

        if ($result) {
            $this->isUpdateSlug = true;

            $object->setSlug('temp_slug_' . time() . rand(0, 1000) . time());
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

    /**
     * {@inheritDoc}
     */
    public function postUpdate(AdminInterface $admin, $object)
    {
        $this->updateSlug($admin, $object);
    }

    public function updateSlug(AdminInterface $admin, $object)
    {
        /** @var $admin AbstractAdmin */

        /** @noinspection PhpUndefinedMethodInspection */
        if (strpos($object->getSlug(), 'temp_slug_') !== false) {
            $service = $admin->getConfigurationPool()->getContainer()->get("sonata.core.slugify.cocur");

            /** @noinspection PhpUndefinedMethodInspection */
            $object->setSlug($service->slugify($object->getName()) . '-' . $object->getId());

            $admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager()->persist($object);
            $admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager()->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function postPersist(AdminInterface $admin, $object)
    {
        $this->updateSlug($admin, $object);
    }

}