<?php

namespace Compo\FaqBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * {@inheritDoc}
 */
class FaqSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $this->setTranslationDomain('CompoFaqBundle');

        $this->setBaseRouteName('admin_compo_faq_faq');

        $builder
            ->setDefaults(
                [
                    'faq_per_page' => 21,
                ]
            );
        $items =
            [
                'faq_per_page' => ['integer'],
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

        $tab->add('faq_per_page', IntegerType::class);
    }
}