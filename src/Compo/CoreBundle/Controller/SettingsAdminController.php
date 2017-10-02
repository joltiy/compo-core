<?php

namespace Compo\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class SettingsAdminController
 *
 * @package Compo\CoreBundle\Controller
 */
class SettingsAdminController extends BaseSettingsAdminController
{
    /**
     * SettingsAdminController constructor.
     */
    public function __construct()
    {
        $this->setTranslationDomain('CompoCoreBundle');
        $this->setNamespase('compo_core_settings');
    }

    public function updateUserSettingsAction(Request $request)
    {
        $code = $request->get('code');
        $fileds = $request->get('fields');

        $user = $this->getUser();

        $userSettings = $user->getSettings();

        $userSettings[$code.'.list.fields'] = $fileds;

        $user->setSettings($userSettings);

        $this->get('doctrine')->getManager()->persist($user);
        $this->get('doctrine')->getManager()->flush();
    }

}