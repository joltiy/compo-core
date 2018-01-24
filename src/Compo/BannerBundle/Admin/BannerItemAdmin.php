<?php

namespace Compo\BannerBundle\Admin;

use Compo\BannerBundle\Entity\BannerItem;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritdoc}
 */
class BannerItemAdmin extends AbstractAdmin
{
    /**
     * Конфигурация админки.
     */
    public function configure()
    {
        $this->configurePosition(true, ['banner']);
        $this->setParentParentAssociationMapping('banner');
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        $this->updateParent($object);
    }

    /**
     * @param $object BannerItem
     */
    public function updateParent($object)
    {
        if ($object->getBanner()) {
            $object->getBanner()->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        $this->updateParent($object);
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove($object)
    {
        $this->updateParent($object);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

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
                '_action',
                null,
                [
                    'actions' => [
                        'edit' => [],
                        'delete' => [],
                    ],
                ]
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
