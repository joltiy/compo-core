<?php

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
        $this->setNamespase('compo_core_settings');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add('update_user_settings', 'update_user_settings');
    }
}
