<?php

namespace Compo\CoreBundle\Settings;

use Compo\MenuBundle\Entity\MenuRepository;
use Compo\Sonata\MediaBundle\Entity\Media;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Mopa\Bundle\BootstrapBundle\Form\Type\TabType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritDoc}
 */
class AdminSettingsSchema extends BaseBundleAdminSettingsSchema
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'action' => $this->getContainer()->get('router')->generate($this->getBaseRouteName() . '_update') . '?',
                'label_format' => 'form.label_settings_%name%',
                'translation_domain' => $this->getTranslationDomain(),
            )
        );
    }
    /**
     * @return array
     * @throws \Exception
     */
    public function getDefaultOptions()
    {
        return array(
            'action' => $this->getContainer()->get('router')->generate(  'compo_core_update') . '?',
            'label_format' => 'form.label_settings_%name%',
            'translation_domain' => $this->getTranslationDomain(),
        );
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

            'header_timework' => '09:00–19:00',
            'header_timework_description' => '<div>
<div>Работаем 6 дней в неделю</div>
<div>Суббота до 15-00</div>
<div>Воскресенье &mdash; выходной</div>
<div>Прием заказов круглосуточно</div>
</div>',

            'header_phones' => '+7 (495) <strong>582-10-10</strong>',

            'footer_menu' => null,

            'footer_copyright' => '<div>
<div>Copyright &copy; 2016.</div>
<div>Интернет-магазин сантехники Dlyavann.ru.</div>
</div>',

            'footer_address' => '<div><span>г. Москва, пересечение Ярославского шоссе и МКАД 95 км (внешняя сторона), магазин &quot;Керамическая плитка. Сантехника&quot;.&nbsp;</span><br/><span>Схема проезда.</span></div>',
            'footer_phones' => '<div>
<div><strong>+7 (495) 582-10-01</strong></div>
<div><strong>+7 (495) 727-75-73</strong></div>
<div><strong>+7 (495) 003-12-29</strong></div>
</div>',

            'logo_image' => null,
        ];

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
            array(
                'label' => 'settings.main_tab',
                'inherit_data' => true,
            )
        );
        $main_tab->add('email', EmailType::class);

        $header_tab = $builder->create(
            'header_tab',
            TabType::class,
            array(
                'label' => 'settings.header_tab',
                'inherit_data' => true,
            )
        );

        $header_tab->add(
            'header_menu',
            ChoiceType::class,
            array(
                'choices' => $this->getMenuRepository()->getChoices()
            )
        );

        $header_tab->add('header_phones', CKEditorType::class);
        $header_tab->add('header_timework', CKEditorType::class);
        $header_tab->add('header_timework_description', CKEditorType::class);


        $footer_tab = $builder->create(
            'footer_tab',
            TabType::class,
            array(
                'label' => 'settings.footer_tab',
                'inherit_data' => true,
            )
        );

        $footer_tab->add(
            'footer_menu',
            ChoiceType::class,
            array(
                'choices' => $this->getMenuRepository()->getChoices()
            )
        );

        $footer_tab->add('footer_copyright', CKEditorType::class);
        $footer_tab->add('footer_address', CKEditorType::class);
        $footer_tab->add('footer_phones', CKEditorType::class);


        $logo_tab = $builder->create(
            'logo_tab',
            TabType::class,
            array(
                'label' => 'settings.logo_tab',
                'inherit_data' => true,
            )
        );

        $logo_tab->add(
            'logo_image',
            MediaType::class,
            array(
                'required' => false,
                'context' => 'default',
                'provider' => 'sonata.media.provider.image',
            )
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
                } else {
                    return new Media();
                }
            },
            function ($media) {
                /** @var $media Media */
                if ($media) {
                    $container = $this->getContainer();

                    $mediaManager = $container->get('sonata.media.manager.media');
                    $mediaManager->save($media);

                    return $media->getId();
                } else {
                    return null;
                }

            }
        );


        $logo_tab->get('logo_image')->addModelTransformer($media_transformer);
    }

    /**
     * @return MenuRepository
     * @throws \Exception
     */
    public function getMenuRepository()
    {
        return $this->getDoctrine()->getRepository('CompoMenuBundle:Menu');
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry|object
     * @throws \Exception
     */
    public function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }
}