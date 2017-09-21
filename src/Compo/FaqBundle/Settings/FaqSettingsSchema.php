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
     * {@inheritDoc}
     */
    public function getDefaultSettings() {
        return [
            'faq_per_page' => 21,
        ];
    }
    
    

    /**
     * {@inheritDoc}
     */
    public function buildFormSettings()
    {
        $tab = $this->addTab('main');

        $tab->add('faq_per_page', IntegerType::class);
    }
}