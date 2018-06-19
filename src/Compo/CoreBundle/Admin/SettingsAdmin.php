<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Admin;

use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class SettingsAdmin.
 */
class SettingsAdmin extends BaseSettingsAdmin
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('CompoCoreBundle');
        $this->setBaseRouteName('compo_core');
        $this->setNamespace('compo_core_settings');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add('update_user_settings', 'update_user_settings');
    }
}
