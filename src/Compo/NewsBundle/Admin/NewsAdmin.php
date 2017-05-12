<?php

namespace Compo\NewsBundle\Admin;

use Compo\NewsBundle\Entity\News;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritDoc}
 */
class NewsAdmin extends AbstractAdmin
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('CompoNewsBundle');
        $this->setSortBy('publicationAt');
        $this->setSortOrder('DESC');
        $this->configureSeo(true);
        $this->configureSettings(true, 'compo_news');
        $this->configureProperties(true);
    }

    /**
     * {@inheritDoc}
     */
    public function configureActionButtons($action, $object = null)
    {
        $list = array();

        if (in_array($action, array('history', 'acl', 'show', 'delete', 'edit'))) {
            $list['show_on_site'] = array(
                'template' => $this->getTemplate('button_show_on_site'),
                'uri' => $this->generatePermalink($this->getSubject())
            );
        } else {
            $list['show_on_site'] = array(
                'template' => $this->getTemplate('button_show_on_site'),
                'uri' => $this->generatePermalink()
            );
        }

        $list = array_merge($list, parent::configureActionButtons($action, $object));

        return $list;
    }

    /**
     * @param $object News
     * @return string
     */
    public function generatePermalink($object = null)
    {
        $manager = $this->getContainer()->get('compo_news.manager.news');

        if (is_null($object)) {
            return $manager->getNewsIndexPermalink();
        } else {
            return $manager->getNewsShowPermalink($object);
        }
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
            ->add('_action', null, array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                    'show_on_site' => array(),
                )
            ));
    }

    /**
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('form.tab_main')
            ->with('form.group_main', array('name' => false, 'class' => 'col-lg-6'))
            ->add('id')
            ->add('enabled')
            ->add('publicationAt')
            ->add('name')
            ->add('description')
            ->add('body')
            ->end()
            ->with('form.group_image', array('name' => false, 'class' => 'col-lg-6'))
            ->add('image')
            ->end()
            ->with('form.group_views', array('name' => false, 'class' => 'col-lg-6'))
            ->add('views')
            ->end()
            ->end();
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