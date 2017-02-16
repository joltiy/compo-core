<?php

namespace Compo\Sonata\AdminBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * {@inheritDoc}
 */
class AbstractAdmin extends BaseAdmin
{
    public $positionEnabled = false;
    public $treeEnabled = false;
    public $em;
    public $postionRelatedFields = array();

    protected $maxPerPage = 50;
    protected $maxPageLinks = 50;
    protected $datagridValues = array(
        '_page' => 1,
        '_per_page' => 50,
    );

    protected $perPageOptions = array(50, 100, 500, 1000, 10000);
    protected $searchResultActions = array('edit');

    protected $settingsNamespace;
    protected $settingsEnabled = false;



    public function configureSettings($settingsEnabled = true, $settingsNamespace)
    {
        $this->setSettingsEnabled($settingsEnabled);
        $this->setSettingsNamespace($settingsNamespace);
    }

    public function setSettingsEnabled($settingsEnabled)
    {
        $this->settingsEnabled = $settingsEnabled;
    }

    /**
     * @return string
     */
    public function getSettingsEnabled()
    {
        return $this->settingsEnabled;
    }

    public function setSettingsNamespace($settingsNamespace)
    {
        $this->settingsNamespace = $settingsNamespace;
    }

    /**
     * @return string
     */
    public function getSettingsNamespace()
    {
        return $this->settingsNamespace;
    }

    public function setParentParentAssociationMapping($parentAssociationMapping)
    {
        $this->parentAssociationMapping = $parentAssociationMapping;
    }

    /**
     * @return array
     */
    public function getPostionRelatedFields()
    {
        return $this->postionRelatedFields;
    }

    /**
     * @param array $postionRelatedFields
     */
    public function setPostionRelatedFields($postionRelatedFields)
    {
        $this->postionRelatedFields = $postionRelatedFields;
    }

    public function setSortOrder($order)
    {
        $this->datagridValues['_sort_order'] = $order;
    }

    public function setSortBy($by)
    {
        $this->datagridValues['_sort_by'] = $by;
    }

    /**
     * @param $positionEnabled
     * @param array $postionRelatedFields
     */
    public function configurePosition($positionEnabled, $postionRelatedFields = array())
    {
        $this->positionEnabled = $positionEnabled;

        $this->postionRelatedFields = $postionRelatedFields;

        if ($positionEnabled) {

            $this->datagridValues = array(
                '_page' => 1,
                '_sort_order' => 'ASC',
                '_sort_by' => 'position',
            );

            $this->addExtension($this->getConfigurationPool()->getContainer()->get("compo.sonata.admin.extension.position"));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        if ($this->treeEnabled && $context == 'list') {
            $query->andWhere(
                $query->expr()->gt($query->getRootAliases()[0] . '.lvl', '0')
            );
        }

        return $query;
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu
     * @param                                               $action
     * @param \Sonata\AdminBundle\Admin\AdminInterface|null $childAdmin
     * @param                                               $route
     * @param bool $route_paramters
     */
    public function configureTabMenuShow(\Knp\Menu\ItemInterface $menu, $action, \Sonata\AdminBundle\Admin\AdminInterface $childAdmin = null, $route, $route_paramters = false)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        // Редактирование
        $menu->addChild(
            $this->trans('tab_menu.link_edit'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        if ($route_paramters === false) {
            $route_paramters = array('id' => $id);
        }

        // Просмотр
        if ($this->hasSubject() && $this->getSubject()->getId() !== null) {
            $menu->addChild(
                $this->trans('tab_menu.link_show_on_site'),
                array('uri' => $admin->getRouteGenerator()->generate($route, $route_paramters), 'linkAttributes' => array('target' => '_blank'))
            );
        }
    }

    /**
     * @param $enabled
     */
    public function configureTree($enabled)
    {
        $this->treeEnabled = $enabled;

        if ($enabled) {
            $this->datagridValues['_sort_by'] = 'lft';
        }
    }

    /**
     * @param $enabled
     */
    public function configureSeo($enabled)
    {
        if ($enabled) {
            $this->addExtension($this->getConfigurationPool()->getContainer()->get("compo_seo.seo.extension"));
        }
    }

    /**
     * @return int
     */
    public function last_position()
    {
        return $this->getConfigurationPool()->getContainer()->get("pix_sortable_behavior.position")->getLastPosition($this->getRoot()->getClass());
    }


    /**
     * @param FormMapper $formMapper
     *
     * @return FormMapper
     */
    public function createImageFormType(FormMapper $formMapper)
    {
        return $formMapper->add('image',
            'sonata_type_model_list',
            array(
                'required' => false
            ),
            array(
                'link_parameters' => array(
                    'context' => 'default',
                    'hide_context' => true,

                ),
            )
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();

        if ($this->treeEnabled) {
            if (!$this->hasRequest()) {
                return $instance;
            }
            if ($parentId = $this->getRequest()->get('parentId')) {
                $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
                $parent = $em->getRepository($this->getClass())->find($parentId);
                $instance->setParent($parent);
            }
        }

        return $instance;
    }


    /**
     * @param      $action
     * @param null $object
     *
     * @return array
     */
    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);

        unset($list['show']);

        return $list;

    }

    /**
     * @param null $name
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($name = null)
    {
        if ($name) {
            return $this->getDoctrine()->getRepository($name);
        } else {
            return $this->getDoctrine()->getRepository($this->getClass());
        }
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry|object
     */
    public function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @return null|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->getConfigurationPool()->getContainer();
    }

    /**
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        if ($this->treeEnabled) {
            $collection->add('tree', 'tree', array('_controller' => $this->baseControllerName . ':tree'));
            $collection->add('move', 'move', array('_controller' => $this->baseControllerName . ':move'));
        }

        if ($this->settingsEnabled && $this->settingsNamespace) {
            $collection->add(
                'settings',
                'settings',
                [
                    '_controller' => $this->getBaseControllerName() . ':settings',
                    'namespace' => $this->settingsNamespace
                ]
            );
        }
    }

    public function initialize()
    {
        $this->setTemplate('button_show_on_site', 'CompoSonataAdminBundle:Button:show_on_site_button.html.twig');
        $this->setTemplate('button_settings', 'CompoSonataAdminBundle:Button:settings_button.html.twig');
        $this->setTemplate('button_tree', 'CompoSonataAdminBundle:Button:tree_button.html.twig');

        parent::initialize();


    }
}