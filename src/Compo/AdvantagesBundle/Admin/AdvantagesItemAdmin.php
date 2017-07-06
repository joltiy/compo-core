<?php

namespace Compo\AdvantagesBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
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
        // Домен переводов
        $this->setTranslationDomain('CompoAdvantagesBundle');

        $this->configurePosition(true, array('advantages'));

        $this->setParentParentAssociationMapping('advantages');

        $this->configureProperties(true);
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
            ->add('url')
            ->add('enabled')
            ->add('_action', null, array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->tab('form.tab_main_advantages', array(
            'translation_domain' => $this->getTranslationDomain()
        ));

        $formMapper->with('form.tab_main', array(
            'name' => false
        ));

        $formMapper->add('id');
        $formMapper->add('enabled');
        $formMapper->add('advantages');
        $formMapper->add('name');
        $formMapper->add('title');
        $formMapper->add('description');
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

    /**
     * {@inheritDoc}
     */
    protected function configureTabMenu(MenuItemInterface $advantages, $action, AdminInterface $childAdmin = null)
    {
        if (in_array($action, array('edit'))) {
            $this->configureTabAdvantagesItem($advantages, $action);

            /** @var AdvantagesAdmin $advantagesAdmin */
            $advantagesAdmin = $this->getConfigurationPool()->getAdminByAdminCode('compo_advantages.admin.advantages');
            $advantagesAdmin->setSubject($this->getSubject()->getAdvantages());
            $tabAdvantages = $advantages->addChild('tab_menu.advantages', array('label' => $this->trans('tab_menu.advantages', array('%name%' => $this->getSubject()->getAdvantages()->getName())), 'attributes' => array('dropdown' => true)));

            $advantagesAdmin->configureTabAdvantagesList($tabAdvantages, $action);
        }
    }

    /**
     * @param MenuItemInterface $advantages
     * @param $action
     * @param AdminInterface|null $childAdmin
     */
    public function configureTabAdvantagesItem(MenuItemInterface $advantages, $action, AdminInterface $childAdmin = null)
    {
        $advantages->addChild(
            $this->trans('tab_menu.link_edit'),
            array('uri' => $this->generateUrl('edit', array('id' => $this->getSubject()->getId())))
        );
    }



    public function preUpdate($object)
    {
        $this->updateAdvantages($object);
    }

    public function prePersist($object)
    {
        $this->updateAdvantages($object);
    }

    public function preRemove($object)
    {
        $this->updateAdvantages($object);
    }

    public function updateAdvantages($object)
    {
        if ($object->getAdvantages()) {
            $object->getAdvantages()->setUpdatedAt(new \DateTime());
        }
    }
}
