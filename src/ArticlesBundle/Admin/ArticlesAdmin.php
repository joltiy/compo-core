<?php

namespace Compo\ArticlesBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

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
        $this->setTranslationDomain('CompoArticlesBundle');
        $this->setSortBy('publicationAt');
        $this->setSortOrder('DESC');
        $this->configureSeo(true);

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
            ->add('deletedAt');
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
            ->add('description')
            ->add('enabled', null, array(
                'editable' => true,
                'required' => true
            ))
            ->add('_action', null, array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                    'show_on_site' => array('template' => 'CompoArticlesBundle:Admin:list__action_show_on_site.html.twig'),

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
            ->with('form.tab_main', array('name' => false))
            ->add('enabled', null, array('required' => false))
            ->add('publicationAt', 'sonata_type_datetime_picker',
                array(
                    'format' => 'dd.MM.y HH:mm:ss',
                    'required' => true,
                )
            )
            ->add('name')
            ->add('description')
            ->add('body')
            ->add('image', 'sonata_type_model_list',
                array(
                    'required' => false,
                    'by_reference' => true,
                ),
                array(
                    'link_parameters' => array(
                        'context' => 'default',
                        'hide_context' => true,
                    ),
                )
            )
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
            ->add('deletedAt');
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (in_array($action, array('edit'))) {

        }

        if (in_array($action, array('list'))) {

        }
    }
}
