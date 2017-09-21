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
     * {@inheritDoc}
     */
    public function getDefaultSettings() {
        return [
            'notification_email_account_default' => $this->getNotificationEmailAccountRepository()->getDefaultId(),
            'notification_email_recipient_default' => '',
        ];
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
        $tab = $this->addTab('main');

        $tab->add(
            'notification_email_account_default',
            ChoiceType::class,
            array(
                'choices' => $this->getNotificationEmailAccountRepository()->getChoices()
            )
        );

        $tab->add('notification_email_recipient_default', TextType::class);
    }
}