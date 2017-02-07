<?php

namespace Compo\ArticlesBundle\Admin;

use Compo\CoreBundle\Admin\BaseSettingsAdmin;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * Настройки ArticlesSettingsAdmin
 *
 * @package Compo\ArticlesBundle\Admin
 */
class ArticlesSettingsAdmin extends BaseSettingsAdmin
{

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('CompoArticlesBundle');
        $this->setBaseRouteName('compo_articles_settings');
        $this->setNamespase('compo_articles_settings');
    }
}