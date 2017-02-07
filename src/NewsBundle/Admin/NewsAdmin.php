<?php

namespace Compo\NewsBundle\Admin;

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
                    'show_on_site' => array('template' => 'CompoNewsBundle:Admin:list__action_show_on_site.html.twig'),

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
            $menu->addChild(
                $this->trans('tab_menu.link_edit'),
                array('uri' => $this->generateUrl('edit', array('id' => $this->getSubject()->getId())))
            );
            $menu->addChild(
                $this->trans('tab_menu.link_show_on_site'),
                array(
                    'uri' => $this->getRouteGenerator()->generate('compo_news_show_by_slug', array('slug' => $this->getSubject()->getSlug())),
                    'linkAttributes' => array('target' => '_blank')
                )
            );
        }

        if (in_array($action, array('list'))) {
            $menu->addChild(
                $this->trans('tab_menu.link_settings'),
                array(
                    'uri' => $this->getRouteGenerator()->generate('compo_news_settings_update', array()),
                )
            );
            $menu->addChild(
                $this->trans('tab_menu.link_show_on_site'),
                array(
                    'uri' => $this->getRouteGenerator()->generate('compo_news_index', array()),
                    'linkAttributes' => array('target' => '_blank')
                )
            );
        }
    }
}
