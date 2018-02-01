<?php

namespace Compo\NewsBundle\Admin;

use Compo\NewsBundle\Entity\News;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Doctrine\ORM\QueryBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Knp\Menu\MenuFactory;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritdoc}
 */
class NewsAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setSortBy('publicationAt');
        $this->setSortOrder('DESC');
    }

    /**
     * {@inheritdoc}
     */
    public function configureActionButtons($action, $object = null)
    {
        $list = [];

        if (in_array($action, ['history', 'acl', 'show', 'delete', 'edit'])) {
            $list['show_on_site'] = [
                'template' => $this->getTemplate('button_show_on_site'),
                'uri' => $this->generatePermalink($this->getSubject()),
            ];
        } else {
            $list['show_on_site'] = [
                'template' => $this->getTemplate('button_show_on_site'),
                'uri' => $this->generatePermalink(),
            ];
        }

        $list = array_merge($list, parent::configureActionButtons($action, $object));

        return $list;
    }

    /**
     * @param $object News
     *
     * @return string
     */
    public function generatePermalink($object = null)
    {
        $manager = $this->getContainer()->get('compo_news.manager.news');

        if (null === $object) {
            return $manager->getNewsIndexPermalink();
        }

        return $manager->getNewsShowPermalink($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('publicationAt');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('publicationAt')
            ->addIdentifier('name')
            ->add('tags')
            ->add('enabled')
            ->add(
                '_action',
                null,
                [
                    'actions' => [
                        'edit' => [],
                        'delete' => [],
                        'show_on_site' => [],
                    ],
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('main')
            ->with('main', ['name' => false, 'class' => 'col-lg-6'])
            ->add('id')
            ->add('enabled')
            ->add('publicationAt')
            ->add('views')
            ->add('name')
            ->add('description', CKEditorType::class, ['attr' => ['class' => ''], 'required' => false])
            ->add('body', CKEditorType::class, ['attr' => ['class' => ''], 'required' => false]);

        /** @var QueryBuilder $tagsQb */
        $tagsQb = $this->getDoctrine()->getManager()->createQueryBuilder('c');
        $tagsQb->select('c')
            ->from('CompoNewsBundle:NewsTag', 'c')
            ->orderBy('c.name', 'ASC');

        $formMapper->add(
            'tags',
            'sonata_type_model',
            [
                'by_reference' => false,
                'multiple' => true,
                'expanded' => false,
                'compound' => false,
                'required' => false,
                'query' => $tagsQb,
            ]
        );

        $formMapper->end()
            ->end();

        $formMapper->tab('media');
        $formMapper->with('media_image');
        $formMapper->add('image');
        $formMapper->end();
        $formMapper->end();
    }

    public function configureAdminNavBar($context, $vars)
    {
        $factory = new MenuFactory();

        $menu = $factory->createItem($context);

        $tabMenuDropdown = $menu->addChild(
            'tab_menu.list_mode.' . $this->getLabel(),
            [
                'label' => $this->getContainer()->get('translator')->trans($this->getLabel(), [], $this->getTranslationDomain()),
                'attributes' => ['dropdown' => true],
            ]
        );

        $menu->setAttribute('icon', 'fa fa-list')->setAttribute('is_dropdown', true)->setAttribute('is_dropdown', true);
        $tabMenuDropdown->setChildrenAttribute('class', 'dropdown-menu');

        $tabMenuDropdown->addChild('list', [
            'uri' => $this->generateUrl('list', []),
            'label' => 'Список',
        ])->setAttribute('icon', 'fa fa-list');

        if ('news_list' === $context) {
        } else {
            $tabMenuDropdown->addChild('edit', [
                'uri' => $this->generateUrl('edit', ['id' => $vars['news']->getId()]),
                'label' => 'Редактировать',
            ])->setAttribute('icon', 'fa fa-pencil');
        }

        return $menu;
    }
}
