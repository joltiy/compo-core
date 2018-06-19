<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\NotificationBundle\Settings;

use Compo\CoreBundle\Settings\BaseBundleAdminSettingsSchema;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * {@inheritdoc}
 */
class NotificationEmailSettingsSchema extends BaseBundleAdminSettingsSchema
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultSettings()
    {
        return [
            'notification_email_account_default' => $this->getNotificationEmailAccountRepository()->getDefaultId(),
            'notification_email_recipient_default' => '',
            'footer_contacts' => '',
        ];
    }

    /**
     * @throws \Exception
     */
    public function getNotificationEmailAccountRepository()
    {
        return $this->getDoctrine()->getRepository('CompoNotificationBundle:NotificationEmailAccount');
    }

    /**
     * {@inheritdoc}
     */
    public function buildFormSettings()
    {
        $tab = $this->addTab('main');

        $tab->add(
            'notification_email_account_default',
            ChoiceType::class,
            [
                'choices' => $this->getNotificationEmailAccountRepository()->getChoices(),
            ]
        );

        $tab->add('notification_email_recipient_default', TextType::class);

        $tab->add('footer_contacts', CKEditorType::class);
    }
}
