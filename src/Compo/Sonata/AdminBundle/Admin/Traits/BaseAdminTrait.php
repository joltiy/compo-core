<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Admin\Traits;

use Compo\ImportBundle\Admin\ExportFormatsTrait;
use Compo\ImportBundle\Admin\ImportFieldHandlerTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Compo\Sonata\UserBundle\Entity\User;
use Knp\Menu\FactoryInterface as MenuFactoryInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\HttpFoundation\Request;

/**
 * Расширение функционала админки.
 */
trait BaseAdminTrait
{
    use ExportFormatsTrait;
    use ParentAssociationMappingTrait;
    use IsUseEntityTraitsTrait;
    use DatagridTableClassTrait;
    use AddListFieldDescriptionTrait;
    use FilterParametersTrait;
    use ConfigureActionButtonsTrait;
    use ImportFieldHandlerTrait;
    use DefineFormBuilderTrait;
    use ConfigureTemplatesTrait;
    use ConfigureListModeTrait;

    /**
     * Конфигурация админки.
     */
    public function initialize()
    {
        // Добавляем режимы отображения
        $this->listModes = [
            'list' => [
                'class' => 'fa fa-list fa-fw',
            ],
        ];

        // Доступные действия для элементов в результатах поиска
        $this->searchResultActions = ['edit'];

        $this->setTemplate('button_settings', 'CompoSonataAdminBundle:Button:settings_button.html.twig');

        parent::initialize();
    }

    /**
     * @param                     $action
     * @param AdminInterface|null $childAdmin
     */
    public function buildTabMenu($action, AdminInterface $childAdmin = null)
    {
        if ($this->loaded['tab_menu']) {
            return;
        }

        $this->loaded['tab_menu'] = true;

        /** @var MenuFactoryInterface $menuFactory */
        $menuFactory = $this->menuFactory;

        $menu = $menuFactory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');
        $menu->setExtra('translation_domain', $this->translationDomain);

        // Prevents BC break with KnpMenuBundle v1.x
        if (method_exists($menu, 'setCurrentUri')) {
            /** @var Request $request */
            $request = $this->getRequest();
            $menu->setCurrentUri($request->getBaseUrl() . $request->getPathInfo());
        }

        $this->configureTabMenu($menu, $action, $childAdmin);

        /** @var AbstractAdmin $childAdmin */
        if ($childAdmin) {
            $childAdmin->configureTabMenu($menu, $action);
        }

        /** @var AbstractAdminExtension $extension */
        foreach ($this->getExtensions() as $extension) {
            /** @var AbstractAdmin $admin */
            $admin = $this;

            $extension->configureTabMenu($admin, $menu, $action, $childAdmin);
        }

        $this->menu = $menu;
    }

    protected function buildList()
    {
        parent::buildList();

        $this->configureTemplates();
        $this->configureListMode();
    }

    /**
     * Направление сортировки по умолчанию.
     *
     * @param string $order
     */
    public function setSortOrder($order)
    {
        $this->datagridValues['_sort_order'] = $order;
    }

    /**
     * Столбец сортировки по умолчанию.
     *
     * @param string $by
     */
    public function setSortBy($by)
    {
        $this->datagridValues['_sort_by'] = $by;
    }

    /**
     * getRepository.
     *
     * @param string $name
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($name = null)
    {
        $doctrine = $this->getDoctrine();

        if ($name) {
            return $doctrine->getRepository($name);
        }

        return $doctrine->getRepository($this->getClass());
    }

    /**
     * getDoctrine.
     *
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * Return the admin related to the given $class.
     *
     * @param string $class
     *
     * @return \Sonata\AdminBundle\Admin\AdminInterface|null
     */
    public function getAdminByClass($class)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this;

        return $admin->getConfigurationPool()->getAdminByClass($class);
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @return User|null
     *
     * @see TokenInterface::getUser()
     */
    public function getUser()
    {
        /** @var AbstractAdmin $admin */
        $admin = $this;

        $container = $admin->getContainer();

        if (!$container->has('security.token_storage')) {
            return null;
        }

        $tokenStorage = $container->get('security.token_storage');

        if (null === $token = $tokenStorage->getToken()) {
            return null;
        }

        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }

    /**
     * getContainer.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        /** @var Pool $configurationPool */
        $configurationPool = $this->getConfigurationPool();

        return $configurationPool->getContainer();
    }
}
