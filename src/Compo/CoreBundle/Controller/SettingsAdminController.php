<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class SettingsAdminController.
 */
class SettingsAdminController extends BaseSettingsAdminController
{
    /**
     * SettingsAdminController constructor.
     */
    public function __construct()
    {
        $this->setTranslationDomain('CompoCoreBundle');
        $this->setNamespace('compo_core_settings');
    }

    /**
     * @param Request $request
     */
    public function updateUserSettingsAction(Request $request)
    {
        /** @var array $settings */
        $settings = $request->get('settings');

        $user = $this->getUser();

        $userSettings = $user->getSettings();

        foreach ($settings as $settingKey => $value) {
            $userSettings[$settingKey] = $value;
        }

        $user->setSettings($userSettings);

        $em = $this->get('doctrine')->getManager();

        $em->persist($user);
        $em->flush();
    }
}
