<?php
/** @noinspection ClassOverridesFieldOfSuperClassInspection */

namespace Compo\Sonata\AdminBundle\Admin;

use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\QueryBuilder;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * {@inheritDoc}
 */
class AbstractAdmin extends BaseAdmin
{
    /**
     * @var bool
     */
    public $positionEnabled = false;
    /**
     * @var bool
     */
    public $treeEnabled = false;
    /**
     * @var
     */
    public $em;
    /**
     * @var array
     */
    public $postionRelatedFields = array();

    /**
     * @var int
     */
    protected $maxPerPage = 50;
    /**
     * @var int
     */
    protected $maxPageLinks = 50;
    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_per_page' => 50,
    );

    /**
     * @var array
     */
    protected $perPageOptions = array(50, 100, 500, 1000, 10000);
    /**
     * @var array
     */
    protected $searchResultActions = array('edit');

    /**
     * @var
     */
    protected $settingsNamespace;
    /**
     * @var bool
     */
    protected $settingsEnabled = false;

    protected $propertiesEnabled = true;

    protected $listFields = array();


    public function isRequiredListField($name) {
        if (in_array($name, array('batch', 'id', 'name', '_action'))) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isPropertiesEnabled(): bool
    {
        return $this->propertiesEnabled;
    }

    /**
     * @param bool $propertiesEnabled
     */
    public function setPropertiesEnabled(bool $propertiesEnabled)
    {
        $this->propertiesEnabled = $propertiesEnabled;
    }


    /**
     * @param bool $settingsEnabled
     * @param $settingsNamespace
     */
    public function configureSettings($settingsEnabled = true, $settingsNamespace)
    {
        $this->setSettingsEnabled($settingsEnabled);
        $this->setSettingsNamespace($settingsNamespace);
    }

    /**
     * @param $key
     */
    public function clearCache($key)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Cache $cacheDriver */
        $cacheDriver = $em->getConfiguration()->getResultCacheImpl();

        $cacheDriver->delete($key);
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
     * @return bool
     */
    public function getSettingsEnabled()
    {
        return $this->settingsEnabled;
    }

    /**
     * @param $settingsEnabled
     */
    public function setSettingsEnabled($settingsEnabled)
    {
        $this->settingsEnabled = $settingsEnabled;
    }

    /**
     * @return string
     */
    public function getSettingsNamespace()
    {
        return $this->settingsNamespace;
    }

    /**
     * @param $settingsNamespace
     */
    public function setSettingsNamespace($settingsNamespace)
    {
        $this->settingsNamespace = $settingsNamespace;
    }

    /**
     * @param $parentAssociationMapping
     */
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

    /**
     * @param $order
     */
    public function setSortOrder($order)
    {
        $this->datagridValues['_sort_order'] = $order;
    }

    /**
     * @param $by
     */
    public function setSortBy($by)
    {
        $this->datagridValues['_sort_by'] = $by;
    }

    /**
     * @param $positionEnabled
     * @param array $postionRelatedFields
     */
    public function configurePosition($positionEnabled, array $postionRelatedFields = array())
    {
        $this->positionEnabled = $positionEnabled;

        $this->postionRelatedFields = $postionRelatedFields;

        if ($positionEnabled) {

            $this->datagridValues = array(
                '_page' => 1,
                '_sort_order' => 'ASC',
                '_sort_by' => 'position',
            );

            //$this->addExtension($this->getConfigurationPool()->getContainer()->get('compo.sonata.admin.extension.position'));
        }
    }

    /**
     * This method is being called by the main admin class and the child class,
     * the getFormBuilder is only call by the main admin class.
     *
     * @param FormBuilderInterface $formBuilder
     */
    public function defineFormBuilder(FormBuilderInterface $formBuilder)
    {
        $mapper = new \Compo\Sonata\AdminBundle\Form\FormMapper($this->getFormContractor(), $formBuilder, $this);

        $this->configureFormFields($mapper);

        foreach ($this->getExtensions() as $extension) {
            $extension->configureFormFields($mapper);
        }

        $this->attachInlineValidator();
    }

    /**
     * {@inheritDoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        if ($this->treeEnabled && $context === 'list') {
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
     * @param bool|array $route_paramters
     */
    public function configureTabMenuShow(\Knp\Menu\ItemInterface $menu, $action, \Sonata\AdminBundle\Admin\AdminInterface $childAdmin = null, $route, $route_paramters = false)
    {
        if (!$childAdmin && 'edit' !== $action) {
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
            $this->addExtension($this->getConfigurationPool()->getContainer()->get('compo_seo.seo.extension'));
        }
    }

    /**
     * @param $enabled
     */
    public function configureProperties($enabled)
    {
        if ($enabled) {
            $this->addExtension($this->getConfigurationPool()->getContainer()->get('compo.sonata.admin.extension.properties'));
        }
    }

    /**
     * @return int
     */
    public function last_position()
    {
        return $this->getConfigurationPool()->getContainer()->get('pix_sortable_behavior.position')->getLastPosition($this->getRoot()->getClass());
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
        return array();

        $list = array();

        if (in_array($action, array('create', 'acl', 'trash', 'history', 'tree', 'show', 'edit', 'delete', 'list', 'batch', 'settings'), true)
            && $this->hasAccess('create')
            && $this->hasRoute('create')
        ) {
            $list['create'] = array(
                'template' => $this->getTemplate('button_create'),
            );
        }

        if (in_array($action, array('edit', 'show', 'trash', 'delete', 'acl', 'history'), true)
            && $this->canAccessObject('edit', $object)
            && $this->hasRoute('edit')
        ) {
            $list['edit'] = array(
                'template' => $this->getTemplate('button_edit'),
            );
        }

        if (in_array($action, array('history', 'show', 'trash', 'edit', 'acl', 'trash'), true)
            && $this->canAccessObject('history', $object)
            && $this->hasRoute('history')
        ) {
            $list['history'] = array(
                'template' => $this->getTemplate('button_history'),
            );


        }

        /*
        if (in_array($action, array('acl', 'edit', 'history'))
            && $this->isAclEnabled()
            && $this->canAccessObject('acl', $object)
            && $this->hasRoute('acl')
        ) {
            $list['acl'] = array(
                'template' => $this->getTemplate('button_acl'),
            );
        }
        */

        if (in_array($action, array('create', 'trash', 'list', 'tree', 'history', 'show', 'edit', 'delete', 'acl', 'batch', 'settings'), true)
            && $this->hasAccess('list')
            && $this->hasRoute('list')
        ) {
            $list['list'] = array(
                'template' => $this->getTemplate('button_list'),
            );
        }

        if (
            in_array($action, array('settings', 'trash', 'batch', 'tree', 'list'), true)
            && $this->hasAccess('acl') && $this->hasRoute('settings')
        ) {
            $list['settings'] = array(
                'template' => $this->getTemplate('button_settings')
            );
        }


        if (
            in_array($action, array('settings', 'trash', 'batch', 'tree', 'list'), true)
            && $this->hasAccess('acl')
        ) {
            if ($this->isUseEntityTraits(
                $this,
                array(
                    'Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity'
                )
            )) {
                $list['trash'] = array(
                    'template' => '@CompoSonataAdmin/Button/trash_button.html.twig',
                );
            }
        }


        return $list;
    }

    /**
     * @param \Sonata\AdminBundle\Admin\AdminInterface $admin
     * @param array $traits
     * @return bool
     */
    public function isUseEntityTraits($admin, array $traits = array())
    {

        $traitsAdmin = class_uses($admin->getClass());

        foreach ($traits as $trait) {
            if (
            !in_array($trait, $traitsAdmin, true)
            ) {
                return false;
            }
        }

        return true;
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
        }

        return $this->getDoctrine()->getRepository($this->getClass());
    }

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        $this->setTemplate('button_show_on_site', 'CompoSonataAdminBundle:Button:show_on_site_button.html.twig');
        $this->setTemplate('button_settings', 'CompoSonataAdminBundle:Button:settings_button.html.twig');
        $this->setTemplate('button_tree', 'CompoSonataAdminBundle:Button:tree_button.html.twig');








        parent::initialize();
    }

    /**
     * {@inheritDoc}
     */
    protected function configureTabMenu(MenuItemInterface $tabMenu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin) {
            if ($this->getSubject() && $action !== 'create') {
                if ($this->hasAccess('edit', $this->getSubject())) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_edit'),
                        array(
                            'uri' => $this->generateUrl('edit', array('id' => $this->getSubject()->getId()))
                        )
                    )->setAttribute('icon', 'fa fa-pencil');
                }



                if ($this->hasAccess('edit', $this->getSubject()) && $this->hasRoute('history')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_history'),
                        array('uri' => $this->generateUrl('history', array('id' => $this->getSubject()->getId())))
                    )->setAttribute('icon', 'fa fa-archive');
                }


            }

            if (
                $action !== 'edit' && $action !== 'history' && $action !== 'delete'
            ) {
                if ($this->hasAccess('list')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_list'),
                        array('uri' => $this->generateUrl('list', array()))
                    )->setAttribute('icon', 'fa fa-list');
                }


                if ($this->hasRoute('trash') && $this->hasAccess('undelete')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_trash'),
                        array('uri' => $this->generateUrl('trash', array()))
                    )->setAttribute('icon', 'fa fa-trash');
                }

                if ($this->hasRoute('create') && $this->hasAccess('create')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_create'),
                        array('uri' => $this->generateUrl('create', array()))
                    )->setAttribute('icon', 'fa fa-plus');
                }

                if (method_exists($this, 'generatePermalink') && $this->generatePermalink()) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_show_on_site'),
                        array('uri' => $this->generatePermalink(), 'linkAttributes' => array('target' => '_blank'))
                    )->setAttribute('icon', 'fa fa-eye');
                }
            }


            if ($this->getSubject() && $action !== 'create') {

                if ($this->hasRoute('clone') && $this->hasAccess('create')) {

                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_clone'),
                        array('uri' => $this->generateUrl('clone', array('id' => $this->getSubject()->getId())))
                    )->setAttribute('icon', 'fa fa-copy');
                }
                $children = $this->getChildren();

                /** @var AdminInterface $child */
                foreach ($children as $child) {

                    if ($child->hasAccess('list')) {
                        $tabMenu->addChild(
                            'tab_menu.link_list_' . $child->getLabel(),
                            array(
                                'label' => $this->trans('tab_menu.title_list', array('%name%' => $this->trans($child->getLabel()))),

                                'uri' => $this->generateUrl($child->getBaseCodeRoute() . '.list', array('id' => $this->getSubject()->getId()))
                            )
                        )->setAttribute('icon', 'fa fa-list');
                    }

                    /*
                    $tabMenuDropdown = $tabMenu->addChild(
                        'tab_menu.' . $child->getLabel(),
                        array(
                            'label' => $this->trans('tab_menu.title_list', array('%name%' => $this->trans($child->getLabel()))),
                            'attributes' => array('dropdown' => true),
                        )
                    );

                    $tabMenuDropdown->addChild(
                        $this->trans('tab_menu.link_list'),
                        array('uri' => $this->generateUrl($child->getBaseCodeRoute() . '.list', array('id' => $this->getSubject()->getId())))
                    );

                    $tabMenuDropdown->addChild(
                        $this->trans('tab_menu.link_create'),
                        array('uri' => $this->generateUrl($child->getBaseCodeRoute() . '.create', array('id' => $this->getSubject()->getId())))
                    );

                    $tabMenuDropdown->addChild(
                        $this->trans('tab_menu.link_trash'),
                        array('uri' => $this->generateUrl($child->getBaseCodeRoute() . '.trash', array('id' => $this->getSubject()->getId())))
                    );
                    */
                }

                if (method_exists($this, 'generatePermalink') && $this->generatePermalink($this->getSubject())) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_show_on_site'),
                        array('uri' => $this->generatePermalink($this->getSubject()), 'linkAttributes' => array('target' => '_blank'))
                    )->setAttribute('icon', 'fa fa-eye');
                }
            }
        } else {



            if ($this->getSubject() && $action !== 'create' && $action !== 'list' && $action !== 'tree' && $action !== 'trash' && $action !== 'untrash') {

                if ($childAdmin->hasAccess('edit', $childAdmin->getSubject())) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_edit'),
                        array('uri' => $childAdmin->generateUrl('edit', array('id' => $childAdmin->getSubject()->getId())))
                    )->setAttribute('icon', 'fa fa-pencil');
                }


                if ($childAdmin->hasRoute('history') && $childAdmin->hasAccess('edit', $childAdmin->getSubject())) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_history'),
                        array('uri' => $childAdmin->generateUrl('history', array('id' => $childAdmin->getSubject()->getId())))
                    )->setAttribute('icon', 'fa fa-archive');
                }

                if ($childAdmin->hasRoute('clone') && $childAdmin->hasAccess('create')) {

                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_clone'),
                        array('uri' => $childAdmin->generateUrl('clone', array('id' => $childAdmin->getSubject()->getId())))
                    )->setAttribute('icon', 'fa fa-copy');
                }

            }

            if ($action === 'create' || $action === 'tree' || $action === 'list' || $action === 'trash' || $action === 'untrash') {

                if ($childAdmin->hasAccess('list')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_list'),
                        array('uri' => $this->generateUrl($childAdmin->getBaseCodeRoute() . '.list', array('id' => $this->getSubject()->getId())))
                    )->setAttribute('icon', 'fa fa-list');
                }

                if ($this->hasRoute('create') && $childAdmin->hasAccess('create')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_create'),
                        array('uri' => $this->generateUrl($childAdmin->getBaseCodeRoute() . '.create', array('id' => $this->getSubject()->getId())))
                    )->setAttribute('icon', 'fa fa-plus');
                }

                if ($childAdmin->hasRoute('trash')) {
                    $tabMenu->addChild(
                        $this->trans('tab_menu.link_trash'),
                        array('uri' => $this->generateUrl($childAdmin->getBaseCodeRoute() . '.trash', array('id' => $this->getSubject()->getId())))
                    )->setAttribute('icon', 'fa fa-trash');
                }

            }



            $children = $childAdmin->getChildren();

            /** @var AdminInterface $child */
            foreach ($children as $child) {
                continue;
                if ($child->hasAccess('list')) {
                    $tabMenu->addChild(
                        'tab_menu.link_list_' . $child->getLabel(),
                        array(
                            'label' => $this->trans('tab_menu.title_list', array('%name%' => $this->trans($child->getLabel()))),

                            'uri' => $child->generateUrl('list', array('id' => $childAdmin->getSubject()->getId()))
                        )
                    )->setAttribute('icon', 'fa fa-list');
                }

                /*
                $tabMenuDropdown = $tabMenu->addChild(
                    'tab_menu.' . $child->getLabel(),
                    array(
                        'label' => $this->trans('tab_menu.title_list', array('%name%' => $this->trans($child->getLabel()))),
                        'attributes' => array('dropdown' => true),
                    )
                );

                $tabMenuDropdown->addChild(
                    $this->trans('tab_menu.link_list'),
                    array('uri' => $this->generateUrl($child->getBaseCodeRoute() . '.list', array('id' => $this->getSubject()->getId())))
                );

                $tabMenuDropdown->addChild(
                    $this->trans('tab_menu.link_create'),
                    array('uri' => $this->generateUrl($child->getBaseCodeRoute() . '.create', array('id' => $this->getSubject()->getId())))
                );

                $tabMenuDropdown->addChild(
                    $this->trans('tab_menu.link_trash'),
                    array('uri' => $this->generateUrl($child->getBaseCodeRoute() . '.trash', array('id' => $this->getSubject()->getId())))
                );
                */
            }


        }
    }

    protected function getAccess()
    {
        $access = array_merge(array(
            'acl' => 'MASTER',
            'export' => 'EXPORT',
            'historyCompareRevisions' => 'EDIT',
            'historyViewRevision' => 'EDIT',
            'history' => 'EDIT',
            'edit' => 'EDIT',
            'show' => 'VIEW',
            'create' => 'CREATE',
            'delete' => 'DELETE',
            'batchDelete' => 'DELETE',
            'list' => 'LIST',
            'tree' => 'LIST',
            'trash' => 'UNDELETE',
            'undelete' => 'UNDELETE',

            'settings' => 'SETTINGS',

        ), $this->getAccessMapping());

        foreach ($this->extensions as $extension) {
            // TODO: remove method check in next major release
            if (method_exists($extension, 'getAccessMapping')) {
                $access = array_merge($access, $extension->getAccessMapping($this));
            }
        }

        return $access;
    }

    /**
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add('clone', $this->getRouterIdParameter() . '/clone');

        //if ($this->manager->hasReader($this->getClass())) {
        $collection->add('history_revert', $this->getRouterIdParameter() . '/history/{revision}/revert');
        //}

        //if ($this->trashManager->hasReader($this->getClass())) {
        $collection->add('trash', 'trash');
        $collection->add('untrash', $this->getRouterIdParameter() . '/untrash');
        //}

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
}