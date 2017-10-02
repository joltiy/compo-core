<?php

namespace Compo\AdvantagesBundle\Admin;

use Compo\AdvantagesBundle\Entity\AdvantagesItem;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritDoc}
 */
class AdvantagesItemAdmin extends AbstractAdmin
{
    /**
     * Конфигурация админки
     */
    public function configure()
    {
        $this->configurePosition(true, array('advantages'));

        $this->setParentParentAssociationMapping('advantages');
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate($object)
    {
        $this->updateAdvantages($object);
    }

    /**
     * @param $object AdvantagesItem
     */
    public function updateAdvantages($object)
    {
        if ($object->getAdvantages()) {
            $object->getAdvantages()->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function prePersist($object)
    {
        $this->updateAdvantages($object);
    }

    /**
     * {@inheritDoc}
     */
    public function preRemove($object)
    {
        $this->updateAdvantages($object);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
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
            ->add('url', null, array(
                'default' => false
            ))
            ->add('enabled')
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
            array(
                'name' => false,
            )
        );

        $formMapper->add('id');
        $formMapper->add('enabled');
        $formMapper->add('advantages');
        $formMapper->add('name');
        $formMapper->add('title');
        $formMapper->add('description', CKEditorType::class, array('attr' => array('class' => ''), 'required' => false));
        $formMapper->add('url');
        $formMapper->add('image');

        $formMapper->end();
        $formMapper->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('url')
            ->add('name')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt');
    }

}