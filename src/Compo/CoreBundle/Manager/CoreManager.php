<?php

namespace Compo\CoreBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

class CoreManager
{
    use ContainerAwareTrait;

    /**
     * @var
     */
    protected $settings;

    /**
     * @return object|\Sylius\Bundle\SettingsBundle\Model\SettingsInterface
     */
    public function getSettings()
    {
        if (null === $this->settings) {
            $this->settings = $this->getContainer()->get('sylius.settings_manager')->load('compo_core_settings');
        }

        return $this->settings;
    }

    public function isDisplayNotify()
    {
        $settings = $this->getSettings();

        $request = $this->getContainer()->get('request_stack')->getCurrentRequest();

        if ($request->get('notify_test')) {
            return true;
        }

        if ($settings['popup_notify_enabled'] && $settings['popup_notify']) {
            $md5 = md5($settings['popup_notify']);

            $session = $this->getContainer()->get('session');

            $hash = $session->get('popup_notify');

            if (!$hash) {
                $session->set('popup_notify', $md5);
                return true;

            }
            if ($hash && $hash !== $md5) {
                $session->set('popup_notify', $md5);

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
