<?php

namespace Compo\FaqBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * {@inheritDoc}
 */
class FaqSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $this->setTranslationDomain('CompoFaqBundle');

        $this->setBaseRouteName('admin_compo_faq_faq');

        $builder
            ->setDefaults(
                [
                    'faq_per_page' => 21,

                    'seo_index_header' => 'Вопрос-ответ',
                    'seo_index_description' => '',
                    'seo_index_title' => 'Вопрос-ответ / {{ site.title|default(site.name) }}',
                    'seo_index_meta_keyword' => 'Вопрос-ответ, {{ site.metaKeyword }}',
                    'seo_index_meta_description' => 'Вопрос-ответ, {{ site.metaDescription }}',

                    'seo_items_header' => '{{ article.header|default(article.name) }}',
                    'seo_items_title' => '{{ article.name }} / {{ site.title|default(site.name) }}',
                    'seo_items_meta_keyword' => '{{ article.name }}, {{ site.metaKeyword }}',
                    'seo_items_meta_description' => '{{ article.name }}, {{ site.metaDescription }}',
                ]
            );
            $items =
                [
                    'faq_per_page' => ['integer'],

                    'seo_index_header' => ['string', 'NULL'],
                    'seo_index_description' => ['string', 'NULL'],
                    'seo_index_title' => ['string', 'NULL'],
                    'seo_index_meta_keyword' => ['string', 'NULL'],
                    'seo_index_meta_description' => ['string', 'NULL'],

                    'seo_items_header' => ['string', 'NULL'],
                    'seo_items_title' => ['string', 'NULL'],
                    'seo_items_meta_keyword' => ['string', 'NULL'],
                    'seo_items_meta_description' => ['string', 'NULL'],
                ];

        foreach ($items as $item_name => $types) {
            $builder->addAllowedTypes($item_name, $types);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function buildFormSettings()
    {
        $this->buildFormTabMain();
        $this->buildFormTabSeoIndex();
        $this->buildFormTabSeoItems();
    }

    /**
     * Основные
     */
    public function buildFormTabMain()
    {
        $tab = $this->addTab('main');

        $tab->add('faq_per_page', IntegerType::class);
    }

    /**
     * SEO для списка
     */
    public function buildFormTabSeoIndex()
    {
        $tab = $this->addTab('seo_index');

        $tab->add('seo_index_header', TextType::class);
        $tab->add('seo_index_description', TextType::class, array('required' => false));
        $tab->add('seo_index_title', TextType::class);
        $tab->add('seo_index_meta_keyword', TextType::class);
        $tab->add('seo_index_meta_description', TextType::class);
    }

    /**
     * SEO для элементов
     */
    public function buildFormTabSeoItems()
    {
        $tab = $this->addTab('seo_items');

        $tab->add('seo_items_header', TextType::class);
        $tab->add('seo_items_title', TextType::class);
        $tab->add('seo_items_meta_keyword', TextType::class);
        $tab->add('seo_items_meta_description', TextType::class);
    }
}