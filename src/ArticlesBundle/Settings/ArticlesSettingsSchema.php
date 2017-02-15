<?php

namespace Compo\ArticlesBundle\Settings;

use Compo\CoreBundle\Settings\BaseAdminSettingsSchema;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Mopa\Bundle\BootstrapBundle\Form\Type\TabType;

/**
 * {@inheritDoc}
 */
class ArticlesSettingsSchema extends BaseAdminSettingsSchema
{
    /**
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $this->setTranslationDomain('CompoArticlesBundle');

        $this->setBaseRouteName('admin_compo_articles_articles');
        //$this->setBaseRoutePattern('/compo/articles/articles/list');

        // Статьи
        // Заголовок - header
        // title - title
        // metaKeyword - metaKeyword
        // metaDescription - metaDescription



        $builder
            ->setDefaults(
                [
                    'articles_per_page' => 21,

                    'seo_header' => 'Статьи',
                    'seo_title' => 'Статьи / {{ site.title }}',
                    'seo_meta_keyword' => 'Статьи, {{ site.metaKeyword }}',
                    'seo_meta_description' => 'Статьи, {{ site.metaDescription }}',

                    'seo_items_header' => '{{ article.name }}',
                    'seo_items_title' => '{{ article.title }} / {{ site.title }}',
                    'seo_items_meta_keyword' => '{{ article.metaKeyword }}, {{ site.metaKeyword }}',
                    'seo_items_meta_description' => '{{ article.metaDescription }}, {{ site.metaDescription }}',
                ]
            )
            ->setAllowedTypes(
                [
                    'articles_per_page' => ['integer'],

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

        $main_tab->add('articles_per_page', IntegerType::class);

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