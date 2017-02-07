<?php

namespace Compo\NewsBundle\Controller;

use Compo\CoreBundle\Controller\BaseSettingsAdminController;

/**
 * Class NewsSettingsAdminController
 *
 * @package Compo\NewsBundle\Controller
 */
class NewsSettingsAdminController extends BaseSettingsAdminController
{
    /**
     * SettingsAdminController constructor.
     */
    public function __construct()
    {
        $this->setTranslationDomain('CompoNewsBundle');
        $this->setNamespase('compo_news_settings');
    }
}