<?php

namespace Compo\MenuBundle\Admin;

use Compo\MenuBundle\Entity\MenuItem;
use Compo\MenuBundle\Entity\MenuItemRepository;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Form\Type\TreeSelectorType;
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
     * {@inheritDoc}
     */
    public function preUpdate($object)
    {
        $this->updateParent($object);
    }

    /**
     * @param $object MenuItem
     */
    public function updateParent($object)
    {
        if ($object->getMenu()) {
            $object->getMenu()->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function prePersist($object)
    {
        $this->updateParent($object);
    }

    /**
     * {@inheritDoc}
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
        $subject = $this->getSubject();

        if (null === $subject) {
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
            $tree = $repository->getForTreeSelector(
                $id,
                function ($qb) use ($root_menu_item) {
                    /** @var QueryBuilder $qb */
                    $qb->andWhere('c.root = ' . $root_menu_item->getId());
                }
            );
        } else {
            // Родительские категории
            $tree = $repository->getForTreeSelector($id);
        }


        $formMapper->tab(
            'form.tab_main_menu',
            array(
                'translation_domain' => $this->getTranslationDomain()
            )
        );

        $formMapper->with(
            'form.tab_main',
            array(
                'name' => false
            )
        );

        $formMapper
            ->add('id')
            ->add('enabled')
            ->add('name')
            ->add('title');

        $formMapper->add(
            'parent',
            TreeSelectorType::class,
            array(
                'current' => $subject,
                'model_manager' => $this->getModelManager(),
                'class' => $this->getClass(),
                'tree' => $tree,
                'required' => true,
            )
        );

        $formMapper
            ->add(
                'type',
                'sonata_type_choice_field_mask',
                array(
                    'choices' => array(
                        'URL' => 'url',
                        'Страница' => 'page',
                        'Тегирование' => 'tagging',
                        'Категория' => 'catalog',
                        'Страна' => 'country',
                        'Производитель' => 'manufacture',
                    ),
                    'map' => array(
                        'url' => array('url'),
                        'page' => array('page'),
                        'tagging' => array('tagging'),
                        'catalog' => array('catalog'),
                        'country' => array('country'),
                        'manufacture' => array('manufacture'),
                    ),
                    'placeholder' => 'Укажите тип',
                    'required' => true
                )
            );


        $query = $this->getDoctrine()->getManager()->createQuery('SELECT p FROM Compo\Sonata\PageBundle\Entity\Page p WHERE p.routeName = \'page_slug\' ORDER BY p.parent ASC, p.position ASC');

        $formMapper->add(
            'page',
            'sonata_type_model',
            array(
                'required' => false,
                'query' => $query
            )
        );

        $query = $this->getDoctrine()->getManager()->createQuery('SELECT p FROM Compo\CountryBundle\Entity\Country p ORDER BY p.name ASC');

        $formMapper->add(
            'country',
            'sonata_type_model',
            array(
                'required' => false,
                'query' => $query
            )
        );

        $query = $this->getDoctrine()->getManager()->createQuery('SELECT p FROM Compo\ManufactureBundle\Entity\Manufacture p ORDER BY p.name ASC');

        $formMapper->add(
            'manufacture',
            'sonata_type_model',
            array(
                'required' => false,
                'query' => $query
            )
        );

        $query = $this->getDoctrine()->getManager()->createQuery('SELECT p FROM Compo\TaggingBundle\Entity\Tagging p ORDER BY p.name ASC');

        $formMapper->add(
            'tagging',
            'sonata_type_model',
            array(
                'required' => false,
                'query' => $query
            )
        );


        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager()->getRepository('CompoCatalogBundle:Catalog')->createQueryBuilder('c');


        $queryBuilder->select('c')
            ->orderBy('c.root, c.lft', 'ASC');

        $tree = $queryBuilder->getQuery()->getResult();

        $formMapper->add(
            'catalog',
            TreeSelectorType::class,
            array(
                'model_manager' => $this->getConfigurationPool()->getContainer()->get('compo_catalog.admin.catalog')->getModelManager(),
                'class' => $this->getConfigurationPool()->getContainer()->get('compo_catalog.admin.catalog')->getClass(),
                'tree' => $tree,
                'required' => true,
            )
        );


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
        if ('edit' === $action) {
            $this->configureTabMenuItem($menu, $action);

            /** @var MenuAdmin $menuAdmin */
            $menuAdmin = $this->getConfigurationPool()->getAdminByAdminCode('compo_menu.admin.menu');
            $menuAdmin->setSubject($this->getSubject()->getMenu());
            $tabMenu = $menu->addChild('tab_menu.menu', array('label' => $this->trans('tab_menu.menu', array('%name%' => $this->getSubject()->getMenu()->getName())), 'attributes' => array('dropdown' => true)));

            $menuAdmin->configureTabMenuList($tabMenu, $action);
        }
    }

    /**
     * @param MenuItemInterface $menu
     * @param $action
     * @param AdminInterface|null $childAdmin
     */
    public function configureTabMenuItem(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        $menu->addChild(
            $this->trans('tab_menu.link_edit'),
            array('uri' => $this->generateUrl('edit', array('id' => $this->getSubject()->getId())))
        );
    }
}
