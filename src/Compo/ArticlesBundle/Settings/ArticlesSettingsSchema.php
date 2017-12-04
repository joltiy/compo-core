<?php

namespace Compo\ArticlesBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * {@inheritdoc}
 */
class ArticlesSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultSettings()
    {
        return array(
            'articles_per_page' => 21,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildFormSettings()
    {
        $tab = $this->addTab('main');

        $tab->add('articles_per_page', IntegerType::class);
    }
}
