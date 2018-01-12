<?php

namespace Compo\FaqBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * {@inheritdoc}
 */
class FaqSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultSettings()
    {
        return [
            'faq_per_page' => 21,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildFormSettings()
    {
        $tab = $this->addTab('main');

        $tab->add('faq_per_page', IntegerType::class);
    }
}
