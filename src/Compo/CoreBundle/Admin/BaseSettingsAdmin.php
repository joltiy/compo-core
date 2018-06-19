<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class SettingsAdmin.
 */
class BaseSettingsAdmin extends AbstractAdmin
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    protected $namespace = 'compo_core_settings';

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     */
    public function __construct($code, $class, $baseControllerName)
    {
        $this->setBaseRouteName('compo_core_settings');
        $this->setBaseRoutePattern('/compo_core_settings');
        $this->setNamespace('compo_core_settings');

        parent::__construct($code, $class, $baseControllerName);
    }

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
        //$collection->clear();

        $collection->add('update', 'settings');

        $collection->add(
            'list',
            'settings',
            [
                '_controller' => 'CompoCoreBundle:SettingsAdmin:update',
                'namespace' => $this->getNamespace(),
            ]
        );
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }
}
