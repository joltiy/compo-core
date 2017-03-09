<?php

namespace Compo\MenuBundle\Admin;

use Compo\MenuBundle\Entity\MenuItemRepository;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Doctrine\DBAL\Query\QueryBuilder;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritDoc}
 */
class MenuItemAdmin extends AbstractAdmin
{
    /**
     * Конфигурация админки
     */
    public function configure()
    {
        // Домен переводов
        $this->setTranslationDomain('CompoMenuBundle');

        // Включение древовидного представления для категорий
        $this->configureTree(true);

        $this->setParentParentAssociationMapping('menu');

        $this->configureProperties(true);

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
            ->add('alias')
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
        $subject = $this->getSubject();

        if (is_null($subject)) {
            $id = null;
            $menu_id = null;

            $root_menu_item = null;

        } else {
            $id = $subject->getId();

            $admin = $this->isChild() ? $this->getParent() : $this;

            $menu_id = $admin->getRequest()->get('id');

            $root_menu_item = $this->getDoctrine()->getRepository('CompoMenuBundle:MenuItem')->findOneBy(array('menu' => $menu_id));
        }

        /** @var MenuItemRepository $repository */
        $repository = $this->getRepository();

        if ($root_menu_item) {
            // Родительские категории

            $tree = $repository->getForTreeSelector($id, function ($qb) use ($root_menu_item) {
                /** @var QueryBuilder $qb */
                $qb->andWhere('c.root = ' . $root_menu_item->getId());
            });
        } else {
            // Родительские категории
            $tree = $repository->getForTreeSelector($id);
        }


        $formMapper->tab('form.tab_main_menu', array(
            'translation_domain' => $this->getTranslationDomain()
        ));


        $formMapper->with('form.tab_main', array(
            'name' => false
        ))
            ->add('id')
            ->add('enabled')
            ->add('name')
            ->add('title')
            ->add('alias');

        $formMapper->add('parent', 'compo_tree_selector', array(
            'current' => $subject,
            'model_manager' => $this->getModelManager(),
            'class' => $this->getClass(),
            'tree' => $tree,
            'required' => true,
        ));

        $formMapper
            ->add('type', 'sonata_type_choice_field_mask', array(
                'choices' => array(
                    'url' => 'URL',
                    'page' => 'Page',
                ),
                'map' => array(
                    'url' => array('url'),
                    'page' => array('page'),
                ),
                'empty_value' => 'Укажите тип',
                'required' => true
            ));


        $query = $this->getDoctrine()->getManager()->createQuery('SELECT p FROM Compo\Sonata\PageBundle\Entity\Page p WHERE p.routeName = \'page_slug\' ORDER BY p.parent ASC, p.position ASC');

        $formMapper->add('page', 'sonata_type_model', array(
            'required' => false,
            'query' => $query
        ));

        $formMapper->add('url');


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
            ->add('alias')
            ->add('name')
            ->add('enabled')
            ->add('lft')
            ->add('lvl')
            ->add('rgt')
            ->add('root')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt');
    }


    /**
     * {@inheritDoc}
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (in_array($action, array('edit'))) {
            $this->configureTabMenuItem($menu, $action);

            /** @var MenuAdmin $menuAdmin */
            $menuAdmin = $this->getConfigurationPool()->getAdminByAdminCode('compo_menu.admin.menu');
            $menuAdmin->setSubject($this->getSubject()->getManufacture());
            $tabMenu = $menu->addChild('tab_menu.menu', array('label' => $this->trans('tab_menu.menu', array('%name%' => $this->getSubject()->getMenu()->getName())), 'attributes' => array('dropdown' => true)));

            $menuAdmin->configureTabMenuList($tabMenu, $action);
        }
    }

    public function configureTabMenuItem(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        $menu->addChild(
            $this->trans('tab_menu.link_edit'),
            array('uri' => $this->generateUrl('edit', array('id' => $this->getSubject()->getId())))
        );

    }
}
