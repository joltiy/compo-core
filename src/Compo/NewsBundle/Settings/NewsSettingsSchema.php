<?php

namespace Compo\NewsBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * {@inheritdoc}
 */
class NewsSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultSettings()
    {
        return array(
            'news_per_page' => 21,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildFormSettings()
    {
        $tab = $this->addTab('main');

        $tab->add('news_per_page', IntegerType::class);
    }
}
