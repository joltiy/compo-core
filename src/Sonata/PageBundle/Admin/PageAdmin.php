<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\Admin;

use Doctrine\DBAL\Query\QueryBuilder;
use Elastica\QueryBuilder\DSL\Query;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Compo\Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Cache\CacheManagerInterface;
use Sonata\PageBundle\Exception\InternalErrorException;
use Sonata\PageBundle\Exception\PageNotFoundException;
use Sonata\PageBundle\Model\PageManagerInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Model\SiteManagerInterface;


class PageAdmin extends Admin
{

    /**
     * @var PageManagerInterface
     */
    protected $pageManager;

    /**
     * @var SiteManagerInterface
     */
    protected $siteManager;

    /**
     * @var CacheManagerInterface
     */
    protected $cacheManager;

    /**
     * {@inheritdoc}
     */
    protected $accessMapping = array(
        'tree' => 'LIST',
        'compose' => 'EDIT',
    );

    /**
     * {@inheritdoc}
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection->add('compose', '{id}/compose', array(
            'id' => null,
        ));
        $collection->add('compose_container_show', 'compose/container/{id}', array(
            'id' => null,
        ));

        $collection->add('tree', 'tree');
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        $object->setEdited(true);
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object)
    {
        if ($this->cacheManager) {
            $this->cacheManager->invalidate(array(
                'page_id' => $object->getId(),
            ));
        }

        $container = $this->getConfigurationPool()->getContainer();

        $container->get('sonata.notification.backend.runtime')->createAndPublish('sonata.page.create_snapshot', array(
            'pageId' => $object->getId()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        $object->setEdited(true);
    }

    /**
     * {@inheritdoc}
     */

    /**
     * @param PageManagerInterface $pageManager
     */
    public function setPageManager(PageManagerInterface $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();

        if (!$this->hasRequest()) {
            return $instance;
        }

        if ($site = $this->getSite()) {
            $instance->setSite($site);
        }

        if ($site && $this->getRequest()->get('url')) {
            $slugs = explode('/', $this->getRequest()->get('url'));
            $slug = array_pop($slugs);

            try {
                $parent = $this->pageManager->getPageByUrl($site, implode('/', $slugs));
            } catch (PageNotFoundException $e) {
                try {
                    $parent = $this->pageManager->getPageByUrl($site, '/');
                } catch (PageNotFoundException $e) {
                    throw new InternalErrorException('Unable to find the root url, please create a route with url = /');
                }
            }

            $instance->setSlug(urldecode($slug));
            $instance->setParent($parent ?: null);
            $instance->setName(urldecode($slug));
        }


        $site = $this->getSites();

        $parent = $this->pageManager->getPageByUrl($site[0], '/');

        if (is_null($instance->getParent())) {
            $instance->setParent($parent);
        }

        return $instance;
    }

    /**
     * @return bool|object|SiteInterface
     *
     * @throws \RuntimeException
     */
    public function getSite()
    {
        if (!$this->hasRequest()) {
            return false;
        }

        $siteId = null;

        if ($this->getRequest()->getMethod() == 'POST') {
            $values = $this->getRequest()->get($this->getUniqid());
            $siteId = isset($values['site']) ? $values['site'] : null;
        }

        $siteId = (null !== $siteId) ? $siteId : $this->getRequest()->get('siteId');

        if ($siteId) {
            $site = $this->siteManager->findOneBy(array('id' => $siteId));

            if (!$site) {
                throw new \RuntimeException('Unable to find the site with id=' . $this->getRequest()->get('siteId'));
            }

            return $site;
        }

        return false;
    }

    /**
     * @return SiteInterface[]
     */
    public function getSites()
    {
        return $this->siteManager->findBy(array());
    }

    /**
     * @param SiteManagerInterface $siteManager
     */
    public function setSiteManager(SiteManagerInterface $siteManager)
    {
        $this->siteManager = $siteManager;
    }

    /**
     * @param CacheManagerInterface $cacheManager
     */
    public function setCacheManager(CacheManagerInterface $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * {@inheritDoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var \Doctrine\ORM\QueryBuilder $query */
        $query = parent::createQuery($context);

        if ($context != 'list') {
            $query->andWhere(
                $query->expr()->eq($query->getRootAliases()[0] . '.routeName', ':routeName')
            );

            $query->setParameter('routeName', 'page_slug');
        }


        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist($object)
    {
        parent::postUpdate($object);

        $container = $this->getConfigurationPool()->getContainer();

        $container->get('sonata.notification.backend.runtime')->createAndPublish('sonata.page.create_snapshot', array(
            'pageId' => $object->getId()
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('site')
            ->add('routeName')
            ->add('pageAlias')
            ->add('type')
            ->add('enabled')
            ->add('decorate')
            ->add('name')
            ->add('slug')
            ->add('customUrl')
            ->add('edited');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper
            ->addIdentifier('name')
            ->add('enabled', null, array('editable' => true));

    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('pageAlias');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sidemenu.link_edit_page'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sidemenu.link_compose_page'),
            array('uri' => $admin->generateUrl('compose', array('id' => $id)))
        );


        if (!$this->getSubject()->isHybrid() && !$this->getSubject()->isInternal()) {
            try {
                $menu->addChild(
                    $this->trans('view_page'),
                    array('uri' => $this->getRouteGenerator()->generate('page_slug', array('path' => $this->getSubject()->getUrl())))
                );
            } catch (\Exception $e) {
                // avoid crashing the admin if the route is not setup correctly
//                throw $e;
            }
        }


    }

    /**
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $site = $this->getSites();


        if ($this->getSubject()) {
            $this->getSubject()->setSite($site[0]);
        }

        // define group zoning
        $formMapper
            ->with('form_page.group_main_label', array('class' => 'col-md-6'))->end()
            ->with('form_page.group_seo_label', array('class' => 'col-md-6'))->end()// ->with('form_page.group_advanced_label', array('class' => 'col-md-6'))->end()
        ;

        if (!$this->getSubject() || (!$this->getSubject()->isInternal() && !$this->getSubject()->isError())) {
            $formMapper
                ->with('form_page.group_main_label')
                ->add('url', 'text', array('attr' => array('readonly' => 'readonly')))
                ->end();
        }

        if (false && $this->hasSubject() && !$this->getSubject()->getId()) {
            $formMapper
                ->with('form_page.group_main_label')
                ->add('site', null, array('required' => true, 'read_only' => true))
                ->end();
        }

        $formMapper
            ->with('form_page.group_main_label')
            ->add('name')
            ->add('enabled', null, array('required' => false))
            ->add('position')
            ->end();

        if (false && $this->hasSubject() && !$this->getSubject()->isInternal()) {
            $formMapper
                ->with('form_page.group_main_label')
                ->add('type', 'sonata_page_type_choice', array('required' => false))
                ->end();
        }

        $formMapper
            ->with('form_page.group_main_label')
            ->add('templateCode', 'sonata_page_template', array('required' => true))
            ->end();


        if (!$this->getSubject() || ($this->getSubject() && $this->getSubject()->getParent()) || ($this->getSubject() && !$this->getSubject()->getId())) {
            $formMapper
                ->with('form_page.group_main_label')
                ->add('parent', 'sonata_page_selector', array(
                    'page' => $this->getSubject() ?: null,
                    'site' => $this->getSubject() ? $this->getSubject()->getSite() : null,
                    'model_manager' => $this->getModelManager(),
                    'class' => $this->getClass(),
                    'required' => false,
                    'filter_choice' => array('hierarchy' => 'root'),
                ), array(
                    'admin_code' => $this->getCode(),
                    'link_parameters' => array(
                        'siteId' => $this->getSubject() ? $this->getSubject()->getSite()->getId() : null,
                    ),
                ))
                ->end();
        }

        if (!$this->getSubject() || !$this->getSubject()->isDynamic()) {
            $formMapper
                ->with('form_page.group_main_label')
                ->add('pageAlias', null, array('required' => false))
                /*
                ->add('target', 'sonata_page_selector', array(
                    'page'          => $this->getSubject() ?: null,
                    'site'          => $this->getSubject() ? $this->getSubject()->getSite() : null,
                    'model_manager' => $this->getModelManager(),
                    'class'         => $this->getClass(),
                    'filter_choice' => array('request_method' => 'all'),
                    'required'      => false,
                ), array(
                    'admin_code'      => $this->getCode(),
                    'link_parameters' => array(
                        'siteId' => $this->getSubject() ? $this->getSubject()->getSite()->getId() : null,
                    ),
                ))
                */

                ->end();
        }

        if (!$this->getSubject() || !$this->getSubject()->isHybrid()) {
            $formMapper
                ->with('form_page.group_seo_label')
                ->add('slug', 'text', array('required' => false))
                //->add('customUrl', 'text', array('required' => false))
                ->end();
        }

        $formMapper
            ->with('form_page.group_seo_label', array('collapsed' => true))
            ->add('title', null, array('required' => false))
            ->add('metaKeyword', 'textarea', array('required' => false))
            ->add('metaDescription', 'textarea', array('required' => false))
            ->end();

        if ($this->hasSubject() && !$this->getSubject()->isCms()) {
            $formMapper
                ->with('form_page.group_advanced_label', array('collapsed' => true))
                ->add('decorate', null, array('required' => false))
                ->end();
        }


        $formMapper->setHelps(array(
            'name' => $this->trans('help_page_name'),
        ));
    }


}
