<?php

namespace Compo\ArticlesBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * {@inheritDoc}
 */
class ArticlesSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * {@inheritDoc}
     */
    public function getDefaultSettings() {
        return [
            'articles_per_page' => 21,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buildFormSettings()
    {
        $tab = $this->addTab('main');

        $tab->add('articles_per_page', IntegerType::class);
    }
}