<?php

namespace Compo\CoreBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

class NotificationManager
{

    use ContainerAwareTrait;

    public function createEmailSubject($template, $vars) {
        $twig = $this->container->get('twig');

        $template = $twig->createTemplate($template);

        return $template->render($vars);
    }

    public function createEmailBody($template, $vars) {
        $twig = $this->container->get('twig');

        $template = $twig->createTemplate($template);

        return $template->render($vars);
    }

    public function createSmsBody($template, $vars) {
        $twig = $this->container->get('twig');

        $template = $twig->createTemplate($template);

        return $template->render($vars);
    }

    public function getFrom() {
        $settings = $this->container->get('sylius.settings.manager')->load('compo_core_settings');

        return $settings->get('notification_email_from');
    }

    public function getAdminNotificationEmails() {
        $settings = $this->container->get('sylius.settings.manager')->load('compo_core_settings');

        $str = $settings->get('notification_email');

        return explode(',', $str);
    }

    public function getAdminNotificationPhones() {
        $settings = $this->container->get('sylius.settings.manager')->load('compo_core_settings');

        $str = $settings->get('notification_phone');

        return explode(',', $str);
    }

    public function sendEmailNotificationUser($email, $subjectTemplate, $messageTemplate, $vars) {
        $mailer = $this->container->get('mailer');

        $message = $mailer->createMessage();

        $subject = $this->createEmailSubject($subjectTemplate, $vars);
        $body = $this->createEmailBody($messageTemplate, $vars);

        $from = $this->getFrom();

        $message->setSubject($subject)
            ->setFrom($from)
            ->setTo($email)
            ->setBody($body, 'text/html');

        $mailer->send($message);
    }


    public function sendEmailNotificationAdmin($subjectTemplate, $messageTemplate, $vars) {

        $mailer = $this->container->get('mailer');

        $message = $mailer->createMessage();

        $subject = $this->createEmailSubject($subjectTemplate, $vars);
        $body = $this->createEmailBody($messageTemplate, $vars);

        $from = $this->getFrom();

        $emails = $this->getAdminNotificationEmails();

        foreach ($emails as $email) {
            $message->setSubject($subject)
                ->setFrom($from)
                ->setTo($email)
                ->setBody($body, 'text/html');

            $mailer->send($message);
        }
    }

    public function getSmsProvider() {
        $settings = $this->container->get('sylius.settings.manager')->load('compo_core_settings');

        $smsAccountId = $settings->get('sms_provider');

        $smsProviderManager = $this->container->get('compo_sms_provider.manager.sms_provider');

        return $smsProviderManager->getSmsProviderByAccountId($smsAccountId);
    }

    public function sendSmsNotificationUser($phone, $messageTemplate, $vars) {
        $provider = $this->getSmsProvider();

        $body = $this->createSmsBody($messageTemplate, $vars);

        $provider->send($phone, $body);

    }

    public function sendSmsNotificationAdmin($messageTemplate, $vars) {
        $provider = $this->getSmsProvider();

        $body = $this->createSmsBody($messageTemplate, $vars);

        $phones = $this->getAdminNotificationPhones();

        foreach ($phones as $phone) {
            $provider->send($phone, $body);
        }
    }

}