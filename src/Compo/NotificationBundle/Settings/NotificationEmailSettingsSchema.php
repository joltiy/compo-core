<?php

namespace Compo\NotificationBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * {@inheritDoc}
 */
class NotificationEmailSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $this->setTranslationDomain('CompoNotificationBundle');

        $this->setBaseRouteName('admin_compo_notification_notificationemail');

        $builder
            ->setDefaults(
                [
                    'notification_email_account_default' => $this->getNotificationEmailAccountRepository()->getDefaultId(),
                    'notification_email_recipient_default' => '',
                ]
            );

        $items =
            [
                'notification_email_account_default' => array('null', 'integer', 'object'),
                'notification_email_recipient_default' => ['string', 'NULL'],
            ];

        foreach ($items as $item_name => $types) {
            $builder->addAllowedTypes($item_name, $types);
        }
    }

    /**
     */
    public function getNotificationEmailAccountRepository()
    {
        return $this->getDoctrine()->getRepository('CompoNotificationBundle:NotificationEmailAccount');
    }

    /**
     * @inheritdoc
     */
    public function buildFormSettings()
    {
        $this->buildFormMainTab();
    }

    /**
     * Основные настройки
     */
    public function buildFormMainTab()
    {
        $tab = $this->addTab('main');

        $tab->add('notification_email_account_default', ChoiceType::class, array(
            'choices' => $this->getNotificationEmailAccountRepository()->getChoices()
        ));

        $tab->add('notification_email_recipient_default', TextType::class);
    }
}