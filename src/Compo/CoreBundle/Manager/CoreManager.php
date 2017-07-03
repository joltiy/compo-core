<?php

namespace Compo\CoreBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;



class CoreManager
{
    use ContainerAwareTrait;

    protected $settings;


    /**
     * @return object|\Sylius\Bundle\SettingsBundle\Model\SettingsInterface
     */
    public function getSettings()
    {
        if (is_null($this->settings)) {
            $this->settings = $this->getContainer()->get('sylius.settings.manager')->load('compo_core_settings');
        }

        return $this->settings;
    }
}