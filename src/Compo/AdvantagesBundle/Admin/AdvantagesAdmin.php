<?php

namespace Compo\AdvantagesBundle\Admin;

use Compo\AdvantagesBundle\Entity\AdvantagesItem;
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
class AdvantagesAdmin extends AbstractAdmin
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('CompoAdvantagesBundle');

        $this->configureProperties(true);
    }


    /**
     * {@inheritDoc}
     */
    public function postRemove($object)
    {
        $advantages_items = $this->getDoctrine()->getRepository('CompoAdvantagesBundle:AdvantagesItem')->findBy(array('advantages' => $object));

        /** @var AdvantagesItem $item */
        foreach ($advantages_items as $item) {
            $item->setDeletedAt(new \DateTime());

            $this->getDoctrine()->getManager()->persist($item);
        }

        $this->getDoctrine()->getManager()->flush();
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
            ->add('description')
            ->add('_action', 'actions', array(
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
        $formMapper
            ->tab('form.tab_main')
            ->with('form.group_main', array('name' => false))
            ->add('id')
            ->add('name')
            ->add('description');

        $formMapper
            ->end()
            ->end();
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

        if (!$childAdmin && in_array($action, array('edit'))) {
            $this->configureTabAdvantagesList($advantages, $action);
        }

        if ($childAdmin && in_array($action, array('list'))) {
            $this->configureTabAdvantagesList($advantages, $action);
        }

        /** @var AdvantagesItemAdmin $childAdmin */
        if ($childAdmin && in_array($action, array('edit'))) {
            $childAdmin->configureTabAdvantagesItem($advantages, $action);

            $tabAdvantages = $advantages->addChild('tab_menu.advantages',
                array(
                    'label' => $this->trans('tab_menu.advantages', array('%name%' => $this->getSubject()->getName())),
                    'attributes' => array('dropdown' => true)
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
