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
 * {@inheritdoc}
 */
class AdvantagesItemAdmin extends AbstractAdmin
{
    /**
     * Конфигурация админки.
     */
    public function configure()
    {
        $this->configurePosition(true, ['advantages']);

        $this->setParentParentAssociationMapping('advantages');
    }

    /**
     * {@inheritdoc}
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

            $this->getContainer()->get('compo_advantages.manager.advantages')->deleteUpdatedAt();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        $this->updateAdvantages($object);
    }

    /**
     * {@inheritdoc}
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
            ->add('url', null, [
                'default' => false,
            ])
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
            [
                'name' => false,
            ]
        );

        $formMapper->add('id');
        $formMapper->add('enabled');
        $formMapper->add('advantages');
        $formMapper->add('name');
        $formMapper->add('title');
        $formMapper->add('description', CKEditorType::class, ['attr' => ['class' => ''], 'required' => false]);
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
