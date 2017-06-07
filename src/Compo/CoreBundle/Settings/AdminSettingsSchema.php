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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * {@inheritDoc}
 */
class AdminSettingsSchema extends BaseAdminSettingsSchema
{
    public function getFormDefaultOptions() {

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
            'footer_payments' => '<ul>
	<li><img alt="Visa" src="http://www.dlyavann.ru/assets/compo/img/payments/visa.png" /></li>
	<li><img alt="Master Card" src="http://www.dlyavann.ru/assets/compo/img/payments/mastercard.png" /></li>
	<li><img alt="Сбербанк Онлайн" src="http://www.dlyavann.ru/assets/compo/img/payments/sberbank.png" /></li>
	<li><img alt="Альфа Клик" src="http://www.dlyavann.ru/assets/compo/img/payments/alfabank-white.png" /></li>
	<li><img alt="Яндекс Деньги" src="http://www.dlyavann.ru/assets/compo/img/payments/yandexmoney.png" /></li>
	<li><img alt="Qiwi" src="http://www.dlyavann.ru/assets/compo/img/payments/qiwi.png" /></li>
</ul>',

            'logo_image' => null,
        ];

        return $options;
    }
    /**
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $this->setTranslationDomain('CompoCoreBundle');
        $this->setBaseRouteName('compo_core_settings');

        $builder
            ->setDefaults(
                $this->getFormDefaultOptions()
            )
            ->setAllowedTypes(
                [
                    'email' => ['string', 'NULL'],


                    'header_menu' => array('null', 'integer', 'object'),

                    'header_timework' => ['string', 'NULL'],
                    'header_timework_description' => ['string', 'NULL'],

                    'header_phones' => ['string', 'NULL'],

                    'footer_menu' => array('null', 'integer', 'object'),

                    'footer_copyright' => ['string', 'NULL'],

                    'footer_address' => ['string', 'NULL'],
                    'footer_phones' => ['string', 'NULL'],
                    'footer_payments' => ['string', 'NULL'],

                    'logo_image' => array('null', 'integer', 'object'),


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
        $main_tab->add('email', EmailType::class);

        $header_tab = $builder->create('header_tab', TabType::class, array(
            'label' => 'settings.header_tab',
            'inherit_data' => true,
        ));

        $header_tab->add('header_menu', ChoiceType::class, array(
            'choices' => $this->getMenuRepository()->getMenuChoices()
        ));

        $header_tab->add('header_phones', CKEditorType::class);
        $header_tab->add('header_timework', CKEditorType::class);
        $header_tab->add('header_timework_description', CKEditorType::class);


        $footer_tab = $builder->create('footer_tab', TabType::class, array(
            'label' => 'settings.footer_tab',
            'inherit_data' => true,
        ));

        $footer_tab->add('footer_menu', ChoiceType::class, array(
            'choices' => $this->getMenuRepository()->getMenuChoices()
        ));

        $footer_tab->add('footer_copyright', CKEditorType::class);
        $footer_tab->add('footer_address', CKEditorType::class);
        $footer_tab->add('footer_phones', CKEditorType::class);
        $footer_tab->add('footer_payments', CKEditorType::class);


        $logo_tab = $builder->create('logo_tab', TabType::class, array(
            'label' => 'settings.logo_tab',
            'inherit_data' => true,
        ));


        $logo_tab->add('logo_image', MediaType::class, array(
            'required' => false,
            'context' => 'default',
            'provider' => 'sonata.media.provider.image',
        ));

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
                    $media = $mediaManager->find($id);

                    return $media;
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
     */
    public function getMenuRepository()
    {
        return $this->getDoctrine()->getRepository('CompoMenuBundle:Menu');
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry|object
     */
    public function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }
}