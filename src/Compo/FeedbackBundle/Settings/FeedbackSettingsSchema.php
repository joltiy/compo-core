<?php

namespace Compo\FeedbackBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;

/**
 * {@inheritDoc}
 */
class FeedbackSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * {@inheritDoc}
     */
    public function getDefaultSettings() {
        return [
        ];
    }
    

    /**
     * {@inheritDoc}
     */
    public function buildFormSettings()
    {
        $this->addTab('main');
    }
}