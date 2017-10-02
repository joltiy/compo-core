<?php

namespace Compo\ArticlesBundle\Admin;

use Compo\ArticlesBundle\Entity\Articles;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
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
}