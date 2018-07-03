<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Settings;

use Compo\MenuBundle\Entity\MenuRepository;
use Compo\Sonata\MediaBundle\Entity\Media;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Mopa\Bundle\BootstrapBundle\Form\Type\TabType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class AdminSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Exception
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'action' => $this->getContainer()->get('router')->generate($this->getBaseRouteName() . '_update') . '?',
                'label_format' => 'form.label_settings_%name%',
                'translation_domain' => $this->getTranslationDomain(),
            ]
        );
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return [
            'action' => $this->getContainer()->get('router')->generate('compo_core_update') . '?',
            'label_format' => 'form.label_settings_%name%',
            'translation_domain' => $this->getTranslationDomain(),
        ];
    }

    /**
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $this->setTranslationDomain('CompoCoreBundle');
        $this->setBaseRouteName('compo_core_settings');

        parent::buildSettings($builder);
    }

    /**
     * @return array
     */
    public function getDefaultSettings()
    {
        $options = [
            'email' => 'info@example.com',
            'header_menu' => null,
            'header_search_placeholder' => 'Поиск среди 100 000 предложений',

            'header_timework' => '09:00–19:00',
            'header_timework_description' => '<div>
<div>Работаем 6 дней в неделю</div>
<div>Суббота до 15-00</div>
<div>Воскресенье &mdash; выходной</div>
<div>Прием заказов круглосуточно</div>
</div>',

            'header_phones' => '+7 (495) <strong>000-00-00</strong>',

            'footer_menu' => null,

            'footer_copyright' => '<div>
<div>Copyright &copy; 2017.</div>
<div>Интернет-магазин.</div>
</div>',

            'footer_address' => '<div><span>г. Москва, &quot;Интернет-магазин&quot;.&nbsp;</span><br/><span>Схема проезда.</span></div>',
            'footer_phones' => '<div>
<div><strong>+7 (495) 000-00-00</strong></div>
</div>',

            'logo_image' => null,
        ];
        $options['popup_notify'] = '';
        $options['popup_notify_enabled'] = false;
        $options['popup_notify_header'] = '';

        return $options;
    }

    /**
     * @throws \Exception
     */
    public function buildFormSettings()
    {
        $builder = $this->getFormBuilder();

        $main_tab = $builder->create(
            'main_tab',
            TabType::class,
            [
                'label' => 'settings.main_tab',
                'inherit_data' => true,
            ]
        );
        $main_tab->add('email', EmailType::class);

        $header_tab = $builder->create(
            'header_tab',
            TabType::class,
            [
                'label' => 'settings.header_tab',
                'inherit_data' => true,
            ]
        );

        $header_tab->add(
            'header_menu',
            ChoiceType::class,
            [
                'choices' => $this->getMenuRepository()->getChoices(),
            ]
        );

        $header_tab->add('header_search_placeholder', TextType::class);

        $header_tab->add('header_phones', CKEditorType::class);
        $header_tab->add('header_timework', CKEditorType::class);
        $header_tab->add('header_timework_description', CKEditorType::class);

        $footer_tab = $builder->create(
            'footer_tab',
            TabType::class,
            [
                'label' => 'settings.footer_tab',
                'inherit_data' => true,
            ]
        );

        $footer_tab->add(
            'footer_menu',
            ChoiceType::class,
            [
                'choices' => $this->getMenuRepository()->getChoices(),
            ]
        );

        $footer_tab->add('footer_copyright', CKEditorType::class);
        $footer_tab->add('footer_address', CKEditorType::class);
        $footer_tab->add('footer_phones', CKEditorType::class);

        $logo_tab = $builder->create(
            'logo_tab',
            TabType::class,
            [
                'label' => 'settings.logo_tab',
                'inherit_data' => true,
            ]
        );

        $logo_tab->add(
            'logo_image',
            MediaType::class,
            [
                'required' => false,
                'context' => 'default',
                'provider' => 'sonata.media.provider.image',
            ]
        );

        $builder
            ->add($main_tab)
            ->add($header_tab)
            ->add($footer_tab)
            ->add($logo_tab);

        $media_transformer = new CallbackTransformer(
            function ($id) {
                if ($id) {
                    $container = $this->getContainer();
                    $mediaManager = $container->get('sonata.media.manager.media');

                    return $mediaManager->find($id);
                }

                return new Media();
            },
            function ($media) {
                /** @var $media Media */
                if ($media) {
                    $container = $this->getContainer();

                    $mediaManager = $container->get('sonata.media.manager.media');
                    $mediaManager->save($media);

                    return $media->getId();
                }

                return null;
            }
        );

        $logo_tab->get('logo_image')->addModelTransformer($media_transformer);

        $notify_tab = $builder->create(
            'notify_tab',
            TabType::class,
            [
                'label' => 'settings.popup_notify_tab',
                'inherit_data' => true,
            ]
        );

        $notify_tab->add('popup_notify_header', TextType::class);

        $notify_tab->add('popup_notify', CKEditorType::class);

        $notify_tab->add('popup_notify_enabled', CheckboxType::class, [
            'required' => false,
        ]);

        $builder->add($notify_tab);
    }

    /**
     * @throws \Exception
     *
     * @return MenuRepository
     */
    public function getMenuRepository()
    {
        return $this->getDoctrine()->getRepository('CompoMenuBundle:Menu');
    }
}
