<?php

namespace Compo\ArticlesBundle\Admin;

use Compo\ArticlesBundle\Entity\Articles;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Knp\Menu\MenuFactory;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritdoc}
 */
class ArticlesAdmin extends AbstractAdmin
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
     * @param $object Articles
     *
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
            ->add('enabled')
            ->add('_action');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('main')
            ->with('main', ['name' => false])
            ->add('id')
            ->add('enabled')
            ->add('publicationAt')
            ->add('views')
            ->add('name')
            ->add('description', CKEditorType::class, ['attr' => ['class' => ''], 'required' => false])
            ->add('body', CKEditorType::class, ['attr' => ['class' => ''], 'required' => false])
            ->end()
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

        if ('article_list' === $context) {
        } else {
            $tabMenuDropdown->addChild('edit', [
                'uri' => $this->generateUrl('edit', ['id' => $vars['article']->getId()]),
                'label' => 'Редактировать',
            ])->setAttribute('icon', 'fa fa-pencil');
        }

        return $menu;
    }
}
