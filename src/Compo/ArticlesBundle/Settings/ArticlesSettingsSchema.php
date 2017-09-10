<?php

namespace Compo\ArticlesBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * {@inheritDoc}
 */
class ArticlesSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $this->setTranslationDomain('CompoArticlesBundle');

        $this->setBaseRouteName('admin_compo_articles_articles');

        $builder
            ->setDefaults(
                [
                    'articles_per_page' => 21,
                ]
            );


        $items =
            [
                'articles_per_page' => ['integer'],
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

        $tab->add('articles_per_page', IntegerType::class);
    }
}