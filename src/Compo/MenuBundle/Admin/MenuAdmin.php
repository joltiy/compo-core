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

                )
            );
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('main')
            ->with('main', array('name' => false))
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


}
