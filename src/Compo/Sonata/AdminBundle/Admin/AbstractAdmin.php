<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\Traits\BaseAdminTrait;
use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * {@inheritdoc}
 */
class AbstractAdmin extends BaseAdmin
{
    use BaseAdminTrait;

    /**
     * @var
     */
    protected $settingsNamespace;

    /**
     * @var bool
     */
    protected $settingsEnabled = false;

    /**
     * @param bool $settingsEnabled
     * @param      $settingsNamespace
     */
    public function configureSettings($settingsEnabled, $settingsNamespace)
    {
        $this->setSettingsEnabled($settingsEnabled);
        $this->setSettingsNamespace($settingsNamespace);
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
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

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
}
