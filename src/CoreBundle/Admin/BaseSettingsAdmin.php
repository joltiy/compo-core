<?php

namespace Compo\CoreBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class SettingsAdmin
 *
 * @package Compo\CoreBundle\Admin
 */
class BaseSettingsAdmin extends AbstractAdmin
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    protected $baseRouteName = 'compo_core_settings';

    /**
     * @var string
     */
    protected $baseRoutePattern = '/compo_core_settings';

    /**
     * @var string
     */
    protected $namespase = 'compo_core_settings';

    /**
     * @return string
     */
    public function getBaseRouteName()
    {
        return $this->baseRouteName;
    }

    /**
     * @param string $baseRouteName
     */
    public function setBaseRouteName($baseRouteName)
    {
        $this->baseRouteName = $baseRouteName;

        $this->setBaseRoutePattern('/' . $baseRouteName);
    }

    /**
     * @return string
     */
    public function getBaseRoutePattern()
    {
        return $this->baseRoutePattern;
    }

    /**
     * @param string $baseRoutePattern
     */
    public function setBaseRoutePattern($baseRoutePattern)
    {
        $this->baseRoutePattern = $baseRoutePattern;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clear();

        $collection->add('update', 'settings');

        $collection->add(
            'list',
            'settings',
            [
                '_controller' => 'Sylius\Bundle\SettingsBundle\Controller\SettingsController::updateAction',
                'namespace' => $this->getNamespase()
            ]
        );
    }

    /**
     * @return string
     */
    public function getNamespase()
    {
        return $this->namespase;
    }

    /**
     * @param string $namespase
     */
    public function setNamespase($namespase)
    {
        $this->namespase = $namespase;
    }
}