<?php

namespace Compo\CoreBundle\Settings;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

class AdminSettingsSchema extends BaseAdminSettingsSchema
{
    /**
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $this->setTranslationDomain('CompoCoreBundle');
        $this->setBaseRouteName('compo_core_settings');

        $builder
            ->setDefaults(
                [
                    'email' => 'info@example.com',

                    'header_timework' => '',
                    'header_timework_description' => '',

                    'header_phones' => '',

                    'footer_copyright' => 'Copyright © 2016.',

                    'footer_address' => '',
                    'footer_phones' => '',
                    'footer_payments' => '',

                ]
            )
            ->setAllowedTypes(
                [
                    'email' => ['string', 'NULL'],

                    'header_timework' => ['string', 'NULL'],
                    'header_timework_description' => ['string', 'NULL'],

                    'header_phones' => ['string', 'NULL'],

                    'footer_copyright' => ['string', 'NULL'],

                    'footer_address' => ['string', 'NULL'],
                    'footer_phones' => ['string', 'NULL'],
                    'footer_payments' => ['string', 'NULL'],
                ]
            );
    }

    /**
     * @param FormBuilderInterface $builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $main_tab = $builder->create('main_tab', 'tab', array(
            'label' => 'settings.main_tab',
            'inherit_data' => true,
        ));
        $main_tab->add('email', 'email');


        $header_tab = $builder->create('header_tab', 'tab', array(
            'label' => 'settings.header_tab',
            'inherit_data' => true,
        ));

        $header_tab->add('header_phones', CKEditorType::class);
        $header_tab->add('header_timework', CKEditorType::class);
        $header_tab->add('header_timework_description', CKEditorType::class);


        $footer_tab = $builder->create('footer_tab', 'tab', array(
            'label' => 'settings.footer_tab',
            'inherit_data' => true,
        ));

        $footer_tab->add('footer_copyright', CKEditorType::class);
        $footer_tab->add('footer_address', CKEditorType::class);
        $footer_tab->add('footer_phones', CKEditorType::class);
        $footer_tab->add('footer_payments', CKEditorType::class);

        $builder
            ->add($main_tab)
            ->add($header_tab)
            ->add($footer_tab);
    }
}