<?php

namespace Compo\NewsBundle\Settings;

use Compo\CoreBundle\Settings\BaseAdminSettingsSchema;
use Mopa\Bundle\BootstrapBundle\Form\Type\TabType;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * {@inheritDoc}
 */
class NewsSettingsSchema extends BaseAdminSettingsSchema
{
    /**
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $this->setTranslationDomain('CompoNewsBundle');

        $this->setBaseRouteName('admin_compo_news_news');

        $builder
            ->setDefaults(
                [
                    'news_per_page' => 21,

                    'seo_header' => 'Новости',
                    'seo_title' => 'Новости / {{ site.title|default(site.name) }}',
                    'seo_meta_keyword' => 'Новости, {{ site.metaKeyword }}',
                    'seo_meta_description' => 'Новости, {{ site.metaDescription }}',

                    'seo_items_header' => '{{ news.header|default(news.name) }}',
                    'seo_items_title' => '{{ news.name }} / {{ site.title|default(site.name) }}',
                    'seo_items_meta_keyword' => '{{ news.metaKeyword }}, {{ site.metaKeyword }}',
                    'seo_items_meta_description' => '{{ news.metaDescription }}, {{ site.metaDescription }}',
                ]
            )
            ->setAllowedTypes(
                [
                    'news_per_page' => ['integer'],

                    'seo_header' => ['string', 'NULL'],
                    'seo_title' => ['string', 'NULL'],
                    'seo_meta_keyword' => ['string', 'NULL'],
                    'seo_meta_description' => ['string', 'NULL'],

                    'seo_items_header' => ['string', 'NULL'],
                    'seo_items_title' => ['string', 'NULL'],
                    'seo_items_meta_keyword' => ['string', 'NULL'],
                    'seo_items_meta_description' => ['string', 'NULL'],
                ]
            );
    }

    /**
     * @param FormBuilderInterface $builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $main_tab = $builder->create('main_tab', TabType::class, array(
            'label' => 'settings.main_tab',
            'inherit_data' => true,
        ));

        $main_tab->add('news_per_page', IntegerType::class);

        $seo_tab = $builder->create('seo_tab', TabType::class, array(
            'label' => 'settings.seo_tab',
            'inherit_data' => true,
        ));

        $seo_tab->add('seo_header', TextType::class);
        $seo_tab->add('seo_title', TextType::class);
        $seo_tab->add('seo_meta_keyword', TextType::class);
        $seo_tab->add('seo_meta_description', TextType::class);

        $seo_items_tab = $builder->create('seo_items_tab', TabType::class, array(
            'label' => 'settings.seo_items_tab',
            'inherit_data' => true,
        ));

        $seo_items_tab->add('seo_items_header', TextType::class);
        $seo_items_tab->add('seo_items_title', TextType::class);
        $seo_items_tab->add('seo_items_meta_keyword', TextType::class);
        $seo_items_tab->add('seo_items_meta_description', TextType::class);

        $builder
            ->add($main_tab)
            ->add($seo_tab)
            ->add($seo_items_tab);
    }

}