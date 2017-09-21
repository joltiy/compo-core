<?php

namespace Compo\CoreBundle\Admin;

/**
 * Class SettingsAdmin
 *
 * @package Compo\CoreBundle\Admin
 */
class SettingsAdmin extends BaseSettingsAdmin
{
    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this->setTranslationDomain('CompoCoreBundle');
        $this->setBaseRouteName('compo_core');
        $this->setNamespase('compo_core_settings');
    }

}