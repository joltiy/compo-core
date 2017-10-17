<?php

namespace Compo\ArticlesBundle\Admin;

use Compo\ArticlesBundle\Entity\Articles;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Knp\Menu\MenuFactory;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Knp\Menu\ItemInterface as MenuItemInterface;

/**
 * {@inheritDoc}
 */
class ArticlesAdmin extends AbstractAdmin
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setSortBy('publicationAt');
        $this->setSortOrder('DESC');
    }

    /**
     * @param $object Articles
     * @return string
     */
    public function generatePermalink($object = null)
    {
        $manager = $this->getContainer()->get('compo_articles.manager.articles');

        if ($object) {
            return $manager->getArticleShowPermalink($object);
        }

        return $manager->getArticlesIndexPermalink();
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('publicationAt')
            ->addIdentifier('name')
            ->add('enabled')
            ->add('_action');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('main')
            ->with('main', array('name' => false))
            ->add('id')
            ->add('enabled')
            ->add('publicationAt')
            ->add('views')
            ->add('name')
            ->add('description', CKEditorType::class, array('attr' => array('class' => ''), 'required' => false))
            ->add('body', CKEditorType::class, array('attr' => array('class' => ''), 'required' => false))
            ->end()
            ->end();


        $formMapper->tab('media');
        $formMapper->with('media_image');
        $formMapper->add('image');
        $formMapper->end();
        $formMapper->end();

    }

    /**
     * {@inheritDoc}
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


    public function configureAdminNavBar($context, $vars) {
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
            'label' => 'Список'
        ))->setAttribute('icon', 'fa fa-list');

        if ($context == 'article_list') {

        } else {
            $tabMenuDropdown->addChild('edit', array(
                'uri' => $this->generateUrl('edit', array('id' => $vars['article']->getId())),
                'label' => 'Редактировать'
            ))->setAttribute('icon', 'fa fa-pencil');
        }

        return $menu;
    }
}
