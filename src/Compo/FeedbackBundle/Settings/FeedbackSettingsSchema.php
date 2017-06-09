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
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $this->setTranslationDomain('CompoFeedbackBundle');

        $this->setBaseRouteName('admin_compo_feedback_feedback');

        $builder
            ->setDefaults(
                [

                ]
            )
            ->setAllowedTypes(
                [

                ]
            );
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
        $this->addTab('main');

    }
}