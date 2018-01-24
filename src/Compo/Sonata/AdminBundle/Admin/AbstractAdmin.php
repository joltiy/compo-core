<?php

namespace Compo\Sonata\AdminBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\Traits\ConfigureTabMenuTrait;
use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * {@inheritdoc}
 */
class AbstractAdmin extends BaseAdmin
{
    use ConfigureTabMenuTrait;

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
    public $postionRelatedFields = [];

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
    protected $datagridValues = [
        '_page' => 1,
        '_per_page' => 50,
    ];

    /**
     * @var array
     */
    protected $perPageOptions = [10, 50, 100, 500, 1000, 10000];
    /**
     * @var array
     */
    protected $searchResultActions = ['edit'];

    /**
     * @var
     */
    protected $settingsNamespace;
    /**
     * @var bool
     */
    protected $settingsEnabled = false;

    protected $propertiesEnabled = true;

    protected $listFields = [];

    protected $listModes = [
        'tree' => [
            'class' => 'fa fa-sitemap',
        ],
        'list' => [
            'class' => 'fa fa-list fa-fw',
        ],
        'mosaic' => [
            'class' => self::MOSAIC_ICON_CLASS,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function addListFieldDescription($name, FieldDescriptionInterface $fieldDescription)
    {
        if (
            'orm_many_to_many' === $fieldDescription->getType()
            &&
            $fieldDescription->getOption('editable', false)
        ) {
            $fieldDescription->setTemplate('@CompoSonataAdmin/SonataDoctrineORMAdminBundle/CRUD/list_orm_many_to_many.html.twig');
        }

        parent::addListFieldDescription($name, $fieldDescription);
    }

    public function getExportFormats()
    {
        return [
             'xlsx', 'csv', 'xml', 'json',
        ];
    }

    public function getExportFields()
    {
        $fields = [];

        /** @var FieldDescription[] $elements */
        $elements = $this->getList()->getElements();

        foreach ($elements as $element) {
            if (in_array($element->getName(), ['batch', '_action'], true)) {
                continue;
            }

            if (!$element->getOption('active')) {
                continue;
            }

            if (ClassMetadataInfo::MANY_TO_ONE === $element->getMappingType()) {
                //$fields[$element->getOption('label')] = $element->getName() . '.' . $element->getOption('associated_property', 'id');
                $fields[$element->getName()] = $element->getName();
            } elseif (ClassMetadataInfo::MANY_TO_MANY === $element->getMappingType()) {
                $fields[$element->getName()] = $element->getName() . 'ExportAsString';
            } elseif (null === $element->getMappingType()) {
            } else {
                $fields[$element->getName()] = $element->getName();
            }
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSourceIterator()
    {
        $datagrid = $this->getDatagrid();
        $datagrid->buildPager();

        $fields = [];

        foreach ($this->getExportFields() as $key => $field) {
            $transLabel = $this->getExportTranslationLabel($key, $field);

            $fields[$transLabel] = $field;
        }

        $dataSourceIterator = $this->getModelManager()->getDataSourceIterator($datagrid, $fields);
        $dataSourceIterator->setDateTimeFormat('d.m.Y H:i:s');

        return $dataSourceIterator;
    }

    public function getExportTranslationLabel($key, $field)
    {
        $label = $this->getTranslationLabel($field, 'export', 'label');
        $transLabel = $this->trans($label);

        if ($transLabel === $label) {
            $label = $this->getTranslationLabel($field, 'list', 'label');
            $transLabel = $this->trans($label);
        }

        if ($transLabel === $label) {
            $label = $this->getTranslationLabel($key, 'export', 'label');
            $transLabel = $this->trans($label);
        }

        if ($transLabel === $label) {
            $label = $this->getTranslationLabel($key, 'list', 'label');
            $transLabel = $this->trans($label);
        }

        if ($transLabel === $label) {
            $transLabel = $key;
        }

        return $transLabel;
    }

    public function getBatchActions()
    {
        $actions = [];

        if ($this->hasRoute('delete') && $this->hasAccess('delete')) {
            $actions['delete'] = [
                'ask_confirmation' => true, // by default always true
            ];
        }

        $actions = $this->configureBatchActions($actions);

        foreach ($this->getExtensions() as $extension) {
            // TODO: remove method check in next major release
            if (method_exists($extension, 'configureBatchActions')) {
                $actions = $extension->configureBatchActions($this, $actions);
            }
        }

        foreach ($actions  as $name => &$action) {
            if (!array_key_exists('label', $action)) {
                $action['label'] = $this->getTranslationLabel($name, 'batch', 'label');
            }

            if (!array_key_exists('translation_domain', $action)) {
                $action['translation_domain'] = $this->getTranslationDomain();
            }
        }

        return $actions;
    }

    /**
     * @return bool
     */
    public function isPropertiesEnabled()
    {
        return $this->propertiesEnabled;
    }

    /**
     * @param bool $propertiesEnabled
     */
    public function setPropertiesEnabled($propertiesEnabled)
    {
        $this->propertiesEnabled = $propertiesEnabled;
    }

    /**
     * @param bool $settingsEnabled
     * @param $settingsNamespace
     */
    public function configureSettings($settingsEnabled, $settingsNamespace)
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
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
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
     * {@inheritdoc}
     */
    public function getFilterParameters()
    {
        $parameters = [];

        // build the values array
        if ($this->hasRequest()) {
            $filters = $this->request->query->get('filter', []);

            // if persisting filters, save filters to session, or pull them out of session if no new filters set
            if ($this->persistFilters) {
                if ($filters === [] && 'reset' !== $this->request->query->get('filters')) {
                    $filters = $this->request->getSession()->get($this->getCode() . '.filter.parameters', []);
                } else {
                    $this->request->getSession()->set($this->getCode() . '.filter.parameters', $filters);
                }
            }

            $parameters = array_merge(
                $this->getModelManager()->getDefaultSortValues($this->getClass()),
                $this->datagridValues,
                $this->getDefaultFilterValues(),
                $filters
            );

            if (!$this->determinedPerPageValue($parameters['_per_page'])) {
                $parameters['_per_page'] = $this->maxPerPage;
            }

            // always force the parent value
            if ($this->isChild() && $this->getParentAssociationMapping()) {
                $name = str_replace('.', '__', $this->getParentAssociationMapping());
                $parameters[$name] = ['value' => $this->request->get($this->getParent()->getIdParameter())];

                if (ClassMetadataInfo::MANY_TO_MANY === $this->getParentAssociationMappingType()) {
                    $parameters[$name] = ['value' => [$this->request->get($this->getParent()->getIdParameter())]];
                } else {
                    $parameters[$name] = ['value' => $this->request->get($this->getParent()->getIdParameter())];
                }
            }
        }

        return $parameters;
    }

    public function getParentAssociationMappingType()
    {
        $name = null;

        $mm = $this->getModelManager();
        if ($mm instanceof ModelManager) {
            // Get associations from this entity to the parent entity (if any)
            $associations = $mm->getMetadata($this->getClass())
                ->getAssociationsByTargetClass($this->getParent()->getClass());
            foreach ($associations as $association) {
                $name = $association['type'];
                break;
            }
        }

        return $name;
    }

    public function getParentAssociationMapping()
    {
        $name = null;

        $mm = $this->getModelManager();
        if ($mm instanceof ModelManager) {
            // Get associations from this entity to the parent entity (if any)
            $associations = $mm->getMetadata($this->getClass())
                ->getAssociationsByTargetClass($this->getParent()->getClass());

            foreach ($associations as $association) {
                // When this admin is child the association must be of the following types
                switch ($association['type']) {
                    case ClassMetadataInfo::MANY_TO_ONE:
                    case ClassMetadataInfo::ONE_TO_ONE:
                        $name = $association['fieldName'];

                        return $name;
                    break;
                    case ClassMetadataInfo::MANY_TO_MANY:
                        $name = $association['fieldName'];
                    break;
                }
            }
        }

        return $name;
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
    public function configurePosition($positionEnabled, array $postionRelatedFields = [])
    {
        $this->positionEnabled = $positionEnabled;

        $this->postionRelatedFields = $postionRelatedFields;

        if ($positionEnabled) {
            //$postionRelatedFields = array_merge($postionRelatedFields, array('position'));

            $this->datagridValues = [
                '_page' => 1,
                '_sort_order' => 'ASC',
                '_sort_by' => 'position',
            ];

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
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        if ($this->treeEnabled && 'list' === $context) {
            $query->andWhere(
                $query->expr()->gt($query->getRootAliases()[0] . '.lvl', '0')
            );
        }

        return $query;
    }

    /**
     * @param \Knp\Menu\ItemInterface                       $menu
     * @param                                               $action
     * @param \Sonata\AdminBundle\Admin\AdminInterface|null $childAdmin
     * @param                                               $route
     * @param bool|array                                    $route_paramters
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
            ['uri' => $admin->generateUrl('edit', ['id' => $id])]
        );

        if (false === $route_paramters) {
            $route_paramters = ['id' => $id];
        }

        // Просмотр
        if ($this->hasSubject() && null !== $this->getSubject()->getId()) {
            $menu->addChild(
                $this->trans('tab_menu.link_show_on_site'),
                ['uri' => $admin->getRouteGenerator()->generate($route, $route_paramters), 'linkAttributes' => ['target' => '_blank']]
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
        return [];

        $list = [];

        if (in_array($action, ['create', 'acl', 'trash', 'history', 'tree', 'show', 'edit', 'delete', 'list', 'batch', 'settings'], true)
            && $this->hasAccess('create')
            && $this->hasRoute('create')
        ) {
            $list['create'] = [
                'template' => $this->getTemplate('button_create'),
            ];
        }

        if (in_array($action, ['edit', 'show', 'trash', 'delete', 'acl', 'history'], true)
            && $this->canAccessObject('edit', $object)
            && $this->hasRoute('edit')
        ) {
            $list['edit'] = [
                'template' => $this->getTemplate('button_edit'),
            ];
        }

        if (in_array($action, ['history', 'show', 'trash', 'edit', 'acl', 'trash'], true)
            && $this->canAccessObject('history', $object)
            && $this->hasRoute('history')
        ) {
            $list['history'] = [
                'template' => $this->getTemplate('button_history'),
            ];
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

        if (in_array($action, ['create', 'trash', 'list', 'tree', 'history', 'show', 'edit', 'delete', 'acl', 'batch', 'settings'], true)
            && $this->hasAccess('list')
            && $this->hasRoute('list')
        ) {
            $list['list'] = [
                'template' => $this->getTemplate('button_list'),
            ];
        }

        if (
            in_array($action, ['settings', 'trash', 'batch', 'tree', 'list'], true)
            && $this->hasAccess('acl') && $this->hasRoute('settings')
        ) {
            $list['settings'] = [
                'template' => $this->getTemplate('button_settings'),
            ];
        }

        if (
            in_array($action, ['settings', 'trash', 'batch', 'tree', 'list'], true)
            && $this->hasAccess('acl')
        ) {
            if ($this->isUseEntityTraits(
                $this,
                [
                    'Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity',
                ]
            )) {
                $list['trash'] = [
                    'template' => '@CompoSonataAdmin/Button/trash_button.html.twig',
                ];
            }
        }

        return $list;
    }

    /**
     * @param \Sonata\AdminBundle\Admin\AdminInterface $admin
     * @param array                                    $traits
     *
     * @return bool
     */
    public function isUseEntityTraits($admin, array $traits = [])
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
     * {@inheritdoc}
     */
    public function initialize()
    {
        $this->setTemplate('button_show_on_site', 'CompoSonataAdminBundle:Button:show_on_site_button.html.twig');
        $this->setTemplate('button_settings', 'CompoSonataAdminBundle:Button:settings_button.html.twig');
        $this->setTemplate('button_tree', 'CompoSonataAdminBundle:Button:tree_button.html.twig');

        $this->setTemplate('outer_list_rows_tree', 'CompoSonataAdminBundle:CRUD:outer_list_rows_tree.html.twig');

        parent::initialize();
    }

    protected function getAccess()
    {
        $access = array_merge([
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
        ], $this->getAccessMapping());

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

        $collection->add('import', 'import', [
            '_controller' => 'CompoSonataImportBundle:Default:index',
        ]);
        $collection->add('upload', '{import_id}/upload', [
            '_controller' => 'CompoSonataImportBundle:Default:upload',
        ]);
        $collection->add('importStatus', '{import_id}/upload/status', [
            '_controller' => 'CompoSonataImportBundle:Default:importStatus',
        ]);

        $collection->add('clone', $this->getRouterIdParameter() . '/clone');
        $collection->add('update_many_to_many', $this->getRouterIdParameter() . '/update_many_to_many');

        //if ($this->manager->hasReader($this->getClass())) {
        $collection->add('history_revert', $this->getRouterIdParameter() . '/history/{revision}/revert');
        //}

        //if ($this->trashManager->hasReader($this->getClass())) {
        $collection->add('trash', 'trash');
        $collection->add('untrash', $this->getRouterIdParameter() . '/untrash');
        //}

        if ($this->treeEnabled) {
            $collection->add('tree', 'tree', ['_controller' => $this->baseControllerName . ':tree']);
            $collection->add('move', 'move', ['_controller' => $this->baseControllerName . ':move']);
        }

        if ($this->settingsEnabled && $this->settingsNamespace) {
            $collection->add(
                'settings',
                'settings',
                [
                    '_controller' => $this->getBaseControllerName() . ':settings',
                    'namespace' => $this->settingsNamespace,
                ]
            );
        }
    }

    /**
     * @param $qb QueryBuilder
     * @param $subject
     * @param $field
     * @param $value
     */
    public function importFieldHandler($qb, $subject, $field, $value)
    {
        $qb->andWhere('entity.name = :value');
        $qb->setParameter('value', $value);
        $qb->setMaxResults(1);
        $qb->setCacheable(true);

        return $qb;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $array = $this->getFormFieldDescriptions();

        foreach ($array as $item) {
            $showMapper->add($item->getName());
        }
    }
}
