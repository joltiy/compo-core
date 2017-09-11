<?php

namespace Compo\NewsBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * {@inheritDoc}
 */
class NewsSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $this->setTranslationDomain('CompoNewsBundle');

        $this->setBaseRouteName('admin_compo_news_news');

        $builder
            ->setDefaults(
                [
                    'news_per_page' => 21,
                ]
            );
        $items =
            [
                'news_per_page' => ['integer'],
            ];

        foreach ($items as $item_name => $types) {
            $builder->addAllowedTypes($item_name, $types);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function buildFormSettings()
    {
        $this->buildFormTabMain();
    }

    /**
     * Основные
     */
    public function buildFormTabMain()
    {
        $tab = $this->addTab('main');

        $tab->add('news_per_page', IntegerType::class);
    }
}