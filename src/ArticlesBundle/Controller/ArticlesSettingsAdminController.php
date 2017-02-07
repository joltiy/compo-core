<?php

namespace Compo\ArticlesBundle\Controller;

use Compo\CoreBundle\Controller\BaseSettingsAdminController;

/**
 * Class ArticlesSettingsAdminController
 *
 * @package Compo\ArticlesBundle\Controller
 */
class ArticlesSettingsAdminController extends BaseSettingsAdminController
{
    /**
     * SettingsAdminController constructor.
     */
    public function __construct()
    {
        $this->setTranslationDomain('CompoArticlesBundle');
        $this->setNamespase('compo_articles_settings');
    }
}