<?php

namespace Compo\MenuBundle\Admin;

use Compo\MenuBundle\Entity\MenuItem;
use Compo\MenuBundle\Entity\MenuItemRepository;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Form\Type\TreeSelectorType;
use Doctrine\DBAL\Query\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritdoc}
 */
class MenuItemAdmin extends AbstractAdmin
{
    /**
     * Конфигурация админки.
     */
    public function configure()
    {
        // Включение древовидного представления для категорий
        $this->configureTree(true);

        $this->setParentParentAssociationMapping('menu');
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        $this->updateParent($object);
    }

    /**
     * {@inheritdoc}
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
                [
                ]
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
            $root_menu_item = $this->getDoctrine()->getRepository('CompoMenuBundle:MenuItem')->findOneBy(['menu' => $menu_id]);
        }

        /** @var MenuItemRepository $repository */
        $repository = $this->getRepository();

        if ($root_menu_item) {
            // Родительские категории
            $tree = $repository->getForTreeSelector(
                $id,
                function ($qb) use ($root_menu_item) {
                    /* @var QueryBuilder $qb */
                    $qb->andWhere('c.root = ' . $root_menu_item->getId());
                }
            );
        } else {
            // Родительские категории
            $tree = $repository->getForTreeSelector($id);
        }

        $formMapper->tab(
            'main_menu',
            [
                'translation_domain' => $this->getTranslationDomain(),
            ]
        );

        $formMapper->with(
            'main',
            [
                'name' => false,
            ]
        );

        $formMapper
            ->add('id')
            ->add('enabled')
            ->add('name')
            ->add('title');

        $formMapper->add(
            'parent',
            TreeSelectorType::class,
            [
                'current' => $subject,
                'model_manager' => $this->getModelManager(),
                'class' => $this->getClass(),
                'tree' => $tree,
                'required' => true,
            ]
        );

        $formMapper
            ->add(
                'type',
                'sonata_type_choice_field_mask',
                [
                    'choices' => [
                        'URL' => 'url',
                        'Страница' => 'page',
                        'Тегирование' => 'tagging',
                        'Категория' => 'catalog',
                        'Страна' => 'country',
                        'Производитель' => 'manufacture',
                    ],
                    'map' => [
                        'url' => ['url'],
                        'page' => ['page'],
                        'tagging' => ['tagging'],
                        'catalog' => ['catalog'],
                        'country' => ['country'],
                        'manufacture' => ['manufacture'],
                    ],
                    'placeholder' => 'Укажите тип',
                    'required' => false,
                ]
            );

        $query = $this->getDoctrine()->getManager()->createQuery('SELECT p FROM Compo\Sonata\PageBundle\Entity\Page p WHERE p.routeName = \'page_slug\' ORDER BY p.parent ASC, p.position ASC');

        $formMapper->add(
            'page',
            'sonata_type_model',
            [
                'required' => false,
                'query' => $query,
            ]
        );

        $query = $this->getDoctrine()->getManager()->createQuery('SELECT p FROM Compo\CountryBundle\Entity\Country p ORDER BY p.name ASC');

        $formMapper->add(
            'country',
            'sonata_type_model',
            [
                'required' => false,
                'query' => $query,
            ]
        );

        $query = $this->getDoctrine()->getManager()->createQuery('SELECT p FROM Compo\ManufactureBundle\Entity\Manufacture p ORDER BY p.name ASC');

        $formMapper->add(
            'manufacture',
            'sonata_type_model',
            [
                'required' => false,
                'query' => $query,
            ]
        );

        $query = $this->getDoctrine()->getManager()->createQuery('SELECT p FROM Compo\TaggingBundle\Entity\Tagging p ORDER BY p.name ASC');

        $formMapper->add(
            'tagging',
            'sonata_type_model',
            [
                'required' => false,
                'query' => $query,
            ]
        );

        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager()->getRepository('CompoCatalogBundle:Catalog')->createQueryBuilder('c');

        $queryBuilder->select('c')
            ->orderBy('c.root, c.lft', 'ASC');

        $tree = $queryBuilder->getQuery()->getResult();

        $formMapper->add(
            'catalog',
            TreeSelectorType::class,
            [
                'model_manager' => $this->getConfigurationPool()->getContainer()->get('compo_catalog.admin.catalog')->getModelManager(),
                'class' => $this->getConfigurationPool()->getContainer()->get('compo_catalog.admin.catalog')->getClass(),
                'tree' => $tree,
                'required' => true,
            ]
        );

        $formMapper->add('url');

        $formMapper->add(
            'target',
            'choice',
            [
                'required' => false,
                'choices' => [
                    'В новом окне' => '_blank',
                ],
                'multiple' => false,
            ]
        );

        $formMapper->add('image');

        $formMapper->end();
        $formMapper->end();
    }
}
