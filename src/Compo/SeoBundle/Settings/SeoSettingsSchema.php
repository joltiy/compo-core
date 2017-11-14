<?php

namespace Compo\SeoBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * {@inheritDoc}
 */
class SeoSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * {@inheritDoc}
     */
    public function getDefaultSettings() {
        return [
            'yandex_metrika_id' => null,
            'google_analytics_id' => '',
            'google_tag_manager_id' => '',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buildFormSettings()
    {
        $yandexTab = $this->addTab('yandex');

        $yandexTab->add('yandex_metrika_id', IntegerType::class);

        $googleTab = $this->addTab('google');

        $googleTab->add('google_analytics_id', TextType::class);
        $googleTab->add('google_tag_manager_id', TextType::class);

    }
}
