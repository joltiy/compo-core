<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\Traits\BaseAdminTrait;
use Compo\Sonata\AdminBundle\Form\Type\TreeSelectorType;
use Compo\Sonata\PageBundle\Entity\Page;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\PageBundle\Form\Type\TemplateChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * {@inheritdoc}
 */
class PageAdmin extends \Sonata\PageBundle\Admin\PageAdmin
{
    use BaseAdminTrait;

    /**
     * {@inheritdoc}
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $routerIdParameter = $this->getRouterIdParameter();

        $collection->add('compose', $routerIdParameter . '/compose', [
            'id' => null,
        ]);
        $collection->add('compose_container_show', 'compose/container/' . $routerIdParameter, [
            'id' => null,
        ]);

        $collection->add('tree', 'tree');
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

        if (null === $instance->getParent()) {
            $site = $this->getSite();

            $parent = $this->pageManager->getPageByUrl($site, '/');

            $instance->setParent($parent);
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        $site = parent::getSite();

        if (!$site) {
            $site = $this->siteManager->findOneBy([], ['id' => 'desc']);
        }

        return $site;
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var \Doctrine\ORM\QueryBuilder $query */
        $query = parent::createQuery($context);

        $rootAliase = $query->getRootAliases()[0];

        if ('list' !== $context) {
            $query->andWhere(
                $query->expr()->eq($rootAliase . '.routeName', ':routeName')
            );

            $query->setParameter('routeName', 'page_slug');
        }

        $query->andWhere(
            $query->expr()->notLike($rootAliase . '.routeName', ':routeNameNotLikeFosUser')
        );
        $query->setParameter('routeNameNotLikeFosUser', 'fos\_user\_%');

        $query->andWhere(
            $query->expr()->notLike($rootAliase . '.routeName', ':routeNameNotLikeFosJs')
        );
        $query->setParameter('routeNameNotLikeFosJs', 'fos\_js\_%');

        $query->andWhere(
            $query->expr()->notLike($rootAliase . '.routeName', ':routeNameNotLikeSonataMedia')
        );
        $query->setParameter('routeNameNotLikeSonataMedia', 'sonata\_media\_%');

        $query->andWhere(
            $query->expr()->notLike($rootAliase . '.routeName', ':routeNameNotLikeSonataPage')
        );
        $query->setParameter('routeNameNotLikeSonataPage', 'sonata\_page\_%');

        $query->andWhere(
            $query->expr()->notLike($rootAliase . '.routeName', ':routeNameNotLikeSonataCache')
        );
        $query->setParameter('routeNameNotLikeSonataCache', 'sonata\_cache\_%');

        return $query;
    }

    /**
     * @param $page Page
     *
     * @return string
     */
    public function generatePermalink($page)
    {
        if (!$page->isHybrid() && !$page->isInternal()) {
            try {
                $path = $page->getUrl();
                $siteRelativePath = $page->getSite()->getRelativePath();
                if (!empty($siteRelativePath)) {
                    $path = $siteRelativePath . $path;
                }

                return $this->getRouteGenerator()->generate(
                    'page_slug',
                    [
                        'path' => $path,
                    ]
                );
            } catch (\Exception $e) {
                // avoid crashing the admin if the route is not setup correctly
                // throw $e;
            }
        }

        return '';
    }

    /**
     * Конфигурация шаблонов.
     */
    public function configureTemplates()
    {
        $this->setTemplate('tree', 'CompoSonataPageBundle:PageAdmin:tree.html.twig');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('url')
            ->add('enabled', null, ['editable' => true]);
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
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if ('edit' !== $action) {
            return;
        }
        $menu->addChild(
            'sidemenu.link_compose_page',
            [
                'uri' => $this->generateObjectUrl('compose', $this->getSubject()),
            ]
        )->setAttribute('icon', 'fa fa-list');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $site = $this->getSites();

        $subject = $this->getSubject();

        if ($subject) {
            $subject->setSite($site[0]);
        }

        // define group zoning
        $formMapper
            ->with('main', ['class' => 'col-md-6'])->end()
            ->with('seo', ['class' => 'col-md-6'])->end()// ->with('form_page.group_advanced_label', array('class' => 'col-md-6'))->end()
        ;

        if (!$subject || (!$subject->isInternal() && !$subject->isError())) {
            $formMapper
                ->with('main')
                ->add('url', 'text', ['attr' => ['readonly' => 'readonly']])
                ->end();
        }

        $formMapper
            ->with('main')
            ->add('name')
            ->add('enabled', null, ['required' => false])
            ->end();

        $formMapper
            ->with('main')
            ->add('templateCode', TemplateChoiceType::class, ['required' => true])
            ->end();

        if (!$subject || ($subject && $subject->getParent())) {
            $pageManager = $this->getContainer()->get('sonata.page.manager.page');

            $formMapper
                ->with('main')
                ->add(
                    'parent',
                    TreeSelectorType::class,
                    [
                        'model_manager' => $this->getModelManager(),
                        'class' => $this->getClass(),
                        'tree' => $pageManager->loadPages($subject->getSite(), 'page_slug', [$subject->getId()]),
                        'required' => true,
                    ]
                )
                ->end();
        }

        if (!$subject || !$subject->isDynamic()) {
            $formMapper
                ->with('main')
                ->add('pageAlias', null, ['required' => false])
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

        if (!$subject || !$subject->isHybrid()) {
            $formMapper
                ->with('seo')
                ->add('slug', 'text', ['required' => false])
                //->add('customUrl', 'text', array('required' => false))
                ->end();
        }

        $formMapper
            ->with('seo', ['collapsed' => true])
            ->add('header', TextType::class, ['required' => false])
            ->add('title', null, ['required' => false])
            ->add('metaKeyword', 'textarea', ['required' => false])
            ->add('metaDescription', 'textarea', ['required' => false])
            ->end();

        if ($subject && !$subject->isCms()) {
            $formMapper
                ->with('form_page.group_advanced_label', ['collapsed' => true])
                ->add('decorate', null, ['required' => false])
                ->end();
        }

        $formMapper->setHelps(
            [
                'name' => $this->trans('help_page_name'),
            ]
        );
    }
}
