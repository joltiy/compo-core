<?php

namespace Compo\CoreBundle\Settings;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * {@inheritDoc}
 */
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

                    'header_timework' => '<div><span>Пн-Пт 9&ndash;18, Сб 9&ndash;15, Вс Вых</span></div>',
                    'header_timework_description' => '<div>
<div>Работаем 6 дней в неделю</div>
<div>Суббота до 15-00</div>
<div>Воскресенье &mdash; выходной</div>
<div>Прием заказов круглосуточно</div>
</div>',

                    'header_phones' => '<div>
<div>+7 (495) 582-10-01</div>
<div>+7 (495) 727-75-73</div>
<div>+7 (495) 003-12-29</div>
</div>',

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