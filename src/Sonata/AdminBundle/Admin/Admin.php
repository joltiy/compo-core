<?php
/**
 * Created by PhpStorm.
 * User: jivoy1988
 * Date: 31.05.16
 * Time: 9:47
 */

namespace Compo\Sonata\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;

/**
 * {@inheritDoc}
 */
class Admin extends BaseAdmin
{
    public $positionEnabled = false;
    public $descriptionFormatterEnabled = false;
    public $treeEnabled = false;
    public $em;

    /**
     * @var FormatterPool
     */
    public $formatterPool;

    /**
     * @param $positionEnabled
     */
    public function configurePosition($positionEnabled)
    {
        $this->positionEnabled = $positionEnabled;

        if ($positionEnabled) {
            $this->datagridValues = array(
                '_page' => 1,
                '_sort_order' => 'ASC',
                '_sort_by' => 'position',
            );

            $this->addExtension($this->getConfigurationPool()->getContainer()->get("compo.sonata.admin.extension.position"));
        }
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        if ($this->treeEnabled && $context == 'list') {
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
     * @param bool                                          $route_paramters
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
            $this->trans('tab_menu.edit_action'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        if ($route_paramters === false) {
            $route_paramters = array('id' => $id);
        }

        // Просмотр
        if ($this->hasSubject() && $this->getSubject()->getId() !== null) {
            $menu->addChild(
                $this->trans('tab_menu.show_on_site_action'),
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
     * @param $enabled
     */
    public function configureDescriptionFormatter($enabled)
    {
        $this->descriptionFormatterEnabled = $enabled;

        if ($enabled) {
            $this->formatterPool = $this->getConfigurationPool()->getContainer()->get('sonata.formatter.pool');
            $this->addExtension($this->getConfigurationPool()->getContainer()->get("compo.sonata.admin.extension.description_formatter"));
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
     * @param FormatterPool $formatterPool
     */
    public function setPoolFormatter(FormatterPool $formatterPool)
    {
        $this->formatterPool = $formatterPool;
    }

    /**
     * @param FormMapper $formMapper
     *
     * @return FormMapper
     */
    public function createDescriptionFormatterFormType(FormMapper $formMapper)
    {
        return $formMapper->add('description', 'sonata_simple_formatter_type', array(
            'required' => false,
            'format' => 'richhtml'
        ));
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
    }
}