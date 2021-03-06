<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
            'google_analytics_view_id' => '',
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
        $googleTab->add('google_analytics_view_id', TextType::class);

        $googleTab->add('google_tag_manager_id', TextType::class);
    }
}
