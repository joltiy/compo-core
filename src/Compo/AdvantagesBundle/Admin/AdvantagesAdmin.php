<?php

namespace Compo\AdvantagesBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritDoc}
 */
class AdvantagesAdmin extends AbstractAdmin
{
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
            ->add('description')
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'edit' => array(),
                        'list' => array('route' => 'compo_advantages.admin.advantages|compo_advantages.admin.advantages_item.list'),
                        'delete' => array(),
                    ),
                )
            );
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->tab('form.tab_main');
        $formMapper->with('form.group_main', array('name' => false));

        $formMapper->add('id');
        $formMapper->add('name');
        $formMapper->add('description', CKEditorType::class, array('attr' => array('class' => ''), 'required' => false));

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
            ->add('name');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureTabMenu(MenuItemInterface $advantages, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && 'edit' === $action) {
            $this->configureTabAdvantagesList($advantages, $action);
        }

        if ($childAdmin && 'list' === $action) {
            $this->configureTabAdvantagesList($advantages, $action);
        }

        /** @var AdvantagesItemAdmin $childAdmin */
        if ($childAdmin && 'edit' === $action) {
            $childAdmin->configureTabAdvantagesItem($advantages, $action);

            $tabAdvantages = $advantages->addChild(
                'tab_menu.advantages',
                array(
                    'label' => $this->trans('tab_menu.advantages', array('%name%' => $this->getSubject()->getName())),
                    'attributes' => array('dropdown' => true),
                )
            );

            $this->configureTabAdvantagesList($tabAdvantages, $action);
        }
    }

    /**
     * @param MenuItemInterface $advantages
     * @param $action
     * @param AdminInterface|null $childAdmin
     */
    public function configureTabAdvantagesList(MenuItemInterface $advantages, $action, AdminInterface $childAdmin = null)
    {
        if ($childAdmin) {
            $subject = $childAdmin->getSubject()->getAdvantages();
        } else {
            $subject = $this->getSubject();
        }

        $advantages->addChild(
            $this->trans('tab_menu.link_edit'),
            array('uri' => $this->generateUrl('edit', array('id' => $subject->getId())))
        );

        $advantages->addChild(
            $this->trans('tab_menu.link_advantages_list'),
            array('uri' => $this->generateUrl('compo_advantages.admin.advantages|compo_advantages.admin.advantages_item.list', array('id' => $subject->getId())))
        );
    }
}