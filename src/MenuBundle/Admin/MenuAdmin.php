<?php

namespace Compo\MenuBundle\Admin;

use Compo\MenuBundle\Entity\MenuItem;
use Compo\Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * {@inheritDoc}
 */
class MenuAdmin extends Admin
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('CompoMenuBundle');
        $this->configurePosition(true);
    }


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('alias')
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
            ->add('alias')
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
            ->with('main_tab', array('name' => false))
            ->add('name')
            ->add('alias')
            ->add('description')
        ;

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



    protected function configureTabMenu(\Knp\Menu\ItemInterface $menu, $action, \Sonata\AdminBundle\Admin\AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);

        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sidemenu.link_edit'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sidemenu.link_view_menu_item'),
            array('uri' => $admin->generateUrl('compo_menu.admin.menu_item.list', array('id' => $id)))
        );
    }

    public function postPersist($menu)
    {
        $menuItem = new MenuItem();

        $menuItem->setName($menu->getName());

        $menuItem->setMenu($menu);

        $this->getDoctrine()->getManager()->persist($menuItem);

        $this->getDoctrine()->getManager()->flush();
    }

    public function postRemove($object)
    {
        $menu_item_root = $this->getDoctrine()->getRepository('CompoMenuBundle:MenuItem')->findOneBy(array('menu' => $object));

        if ($menu_item_root) {
            $menu_item_root->setDeletedAt(new \DateTime());
            $this->getDoctrine()->getManager()->persist($menu_item_root);

            $items = $this->getDoctrine()->getRepository('CompoMenuBundle:MenuItem')->getChildren($menu_item_root);

            foreach ($items as $item) {
                $item->setDeletedAt(new \DateTime());

                $this->getDoctrine()->getManager()->persist($item);
            }

            $this->getDoctrine()->getManager()->flush();
        }

    }
}
