<?php

namespace Compo\FeedbackBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;

/**
 * {@inheritdoc}
 */
class FeedbackSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultSettings()
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildFormSettings()
    {
        $this->addTab('main');
    }
}
