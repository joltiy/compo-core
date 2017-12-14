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
        $list = array();

        if (in_array($action, array('history', 'acl', 'show', 'delete', 'edit'), true)) {
            $list['show_on_site'] = array(
                'template' => $this->getTemplate('button_show_on_site'),
                'uri' => $this->generatePermalink($this->getSubject()),
            );
        } else {
            $list['show_on_site'] = array(
                'template' => $this->getTemplate('button_show_on_site'),
                'uri' => $this->generatePermalink(),
            );
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
                array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        'show_on_site' => array(),
                    ),
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('main')
            ->with('main', array('name' => false, 'class' => 'col-lg-6'))
            ->add('id')
            ->add('enabled')
            ->add('publicationAt')
            ->add('views')
            ->add('name')
            ->add('description', CKEditorType::class, array('attr' => array('class' => ''), 'required' => false))
            ->add('body', CKEditorType::class, array('attr' => array('class' => ''), 'required' => false));

        /** @var QueryBuilder $tagsQb */
        $tagsQb = $this->getDoctrine()->getManager()->createQueryBuilder('c');
        $tagsQb->select('c')
            ->from('CompoNewsBundle:NewsTag', 'c')
            ->orderBy('c.name', 'ASC');

        $formMapper->add(
            'tags',
            'sonata_type_model',
            array(
                'by_reference' => false,
                'multiple' => true,
                'expanded' => false,
                'compound' => false,
                'required' => false,
                'query' => $tagsQb,
            )
        );

        $formMapper->end()
            ->end();

        $formMapper->tab('media');
        $formMapper->with('media_image');
        $formMapper->add('image');
        $formMapper->end();
        $formMapper->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('publicationAt');
    }

    public function configureAdminNavBar($context, $vars)
    {
        $factory = new MenuFactory();

        $menu = $factory->createItem($context);

        $tabMenuDropdown = $menu->addChild(
            'tab_menu.list_mode.' . $this->getLabel(),
            array(
                'label' => $this->getContainer()->get('translator')->trans($this->getLabel(), array(), $this->getTranslationDomain()),
                'attributes' => array('dropdown' => true),
            )
        );

        $menu->setAttribute('icon', 'fa fa-list')->setAttribute('is_dropdown', true)->setAttribute('is_dropdown', true);
        $tabMenuDropdown->setChildrenAttribute('class', 'dropdown-menu');

        $tabMenuDropdown->addChild('list', array(
            'uri' => $this->generateUrl('list', array()),
            'label' => 'Список',
        ))->setAttribute('icon', 'fa fa-list');

        if ('news_list' == $context) {
        } else {
            $tabMenuDropdown->addChild('edit', array(
                'uri' => $this->generateUrl('edit', array('id' => $vars['news']->getId())),
                'label' => 'Редактировать',
            ))->setAttribute('icon', 'fa fa-pencil');
        }

        return $menu;
    }
}
