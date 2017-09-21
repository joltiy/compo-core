<?php

namespace Compo\NewsBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * {@inheritDoc}
 */
class NewsSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * {@inheritDoc}
     */
    public function getDefaultSettings() {
        return [
            'news_per_page' => 21,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buildFormSettings()
    {
        $tab = $this->addTab('main');

        $tab->add('news_per_page', IntegerType::class);
    }
}