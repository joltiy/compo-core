<?php

namespace Compo\MenuBundle\Admin;

use Compo\MenuBundle\Entity\Menu;
use Compo\MenuBundle\Entity\MenuItem;
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
class MenuAdmin extends AbstractAdmin
{
    /**
     * {@inheritDoc}
     */
    public function postPersist($menu)
    {
        /** @var Menu $menu */

        $menuItem = new MenuItem();

        $menuItem->setName($menu->getName());

        $menuItem->setMenu($menu);

        $this->getDoctrine()->getManager()->persist($menuItem);

        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function postRemove($object)
    {
        $menu_item_root = $this->getDoctrine()->getRepository('CompoMenuBundle:MenuItem')->findOneBy(array('menu' => $object));

        if ($menu_item_root) {
            $menu_item_root->setDeletedAt(new \DateTime());
            $this->getDoctrine()->getManager()->persist($menu_item_root);

            $items = $this->getDoctrine()->getRepository('CompoMenuBundle:MenuItem')->getChildren($menu_item_root);

            /** @var MenuItem $item */
            foreach ($items as $item) {
                $item->setDeletedAt(new \DateTime());

                $this->getDoctrine()->getManager()->persist($item);
            }

            $this->getDoctrine()->getManager()->flush();
        }
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
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                    )
                )
            );
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
            ->add('description', CKEditorType::class, array('attr' => array('class' => ''), 'required' => false));

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
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && 'edit' === $action) {
            $this->configureTabMenuList($menu, $action);
        }

        if ($childAdmin && 'list' === $action) {
            $this->configureTabMenuList($menu, $action);
        }

        /** @var MenuItemAdmin $childAdmin */
        if ($childAdmin && 'edit' === $action) {
            $childAdmin->configureTabMenuItem($menu, $action);

            $tabMenu = $menu->addChild(
                'tab_menu.menu',
                array(
                    'label' => $this->trans('tab_menu.menu', array('%name%' => $this->getSubject()->getName())),
                    'attributes' => array('dropdown' => true)
                )
            );

            $this->configureTabMenuList($tabMenu, $action);
        }
    }

    /**
     * @param MenuItemInterface $menu
     * @param $action
     * @param AdminInterface|null $childAdmin
     */
    public function configureTabMenuList(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if ($childAdmin) {
            $subject = $childAdmin->getSubject()->getMenu();
        } else {
            $subject = $this->getSubject();
        }

        $menu->addChild(
            $this->trans('tab_menu.link_edit'),
            array('uri' => $this->generateUrl('edit', array('id' => $subject->getId())))
        );

        $menu->addChild(
            $this->trans('tab_menu.link_menu_list'),
            array('uri' => $this->generateUrl('compo_menu.admin.menu|compo_menu.admin.menu_item.tree', array('id' => $subject->getId())))
        );
    }
}
