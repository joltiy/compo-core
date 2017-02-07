<?php

namespace Compo\NewsBundle\Admin;

use Compo\CoreBundle\Admin\BaseSettingsAdmin;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * Настройки NewsSettingsAdmin
 *
 * @package Compo\NewsBundle\Admin
 */
class NewsSettingsAdmin extends BaseSettingsAdmin
{

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('CompoNewsBundle');
        $this->setBaseRouteName('compo_news_settings');
        $this->setNamespase('compo_news_settings');
    }
}