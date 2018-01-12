<?php

namespace Compo\SeoBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * {@inheritdoc}
 */
class SeoSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultSettings()
    {
        return [
            'yandex_metrika_id' => null,
            'google_analytics_id' => '',
            'google_tag_manager_id' => '',
        ];
    }

    /**
     * {@inheritdoc}
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
