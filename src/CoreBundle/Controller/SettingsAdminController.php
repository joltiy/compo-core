<?php

namespace Compo\CoreBundle\Controller;

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
}