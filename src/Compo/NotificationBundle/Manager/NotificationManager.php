<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\NotificationBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\NotificationBundle\Entity\NotificationEmail;
use Compo\NotificationBundle\Entity\NotificationEmailAccount;
use Compo\NotificationBundle\Entity\NotificationSms;
use Compo\NotificationBundle\Sms\SmsRuTransport;

/**
 * Class NotificationManager.
 */
class NotificationManager
{
    use ContainerAwareTrait;

    public const EMAIL_AUTH_MODE_PLAIN = 'plain';

    public const EMAIL_AUTH_MODE_LOGIN = 'login';

    public const EMAIL_AUTH_MODE_CRAM_MD5 = 'cram-md5';

    /**
     * @var array
     */
    public $defaultEvents = [];

    /**
     * @var NotificationEmailAccount
     */
    public $defaultSender;

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function getDefaultEventByName($name)
    {
        foreach ($this->getDefaultEvents() as $event) {
            if ($event['name'] === $name) {
                return $event;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getDefaultEvents()
    {
        return $this->defaultEvents;
    }

    /**
     * @param array $defaultEvents
     */
    public function setDefaultEvents($defaultEvents)
    {
        $this->defaultEvents = $defaultEvents;
    }

    /**
     * @param $name
     *
     * @return array
     */
    public function getDefaultEventsByEvent($name)
    {
        $events = [];

        foreach ($this->getDefaultEvents() as $event) {
            if ($event['event'] === $name) {
                $events[] = $event;
            }
        }

        return $events;
    }

    /**
     * @return array
     */
    public function getEmailTransport()
    {
        return [
            'SMTP' => 'smtp',
            'SendMail' => 'sendmail',
        ];
    }

    /**
     * @return array
     */
    public function getSmsTransport()
    {
        return [
            'sms.ru' => 'sms',
        ];
    }

    /**
     * @return array
     */
    public function getEmailEncryption()
    {
        return [
            'TLS' => 'tls',
            'SSL' => 'ssl',
        ];
    }

    /**
     * @return array
     */
    public static function getEmailAuthModeList()
    {
        return [
            self::EMAIL_AUTH_MODE_PLAIN => self::EMAIL_AUTH_MODE_PLAIN,
            self::EMAIL_AUTH_MODE_LOGIN => self::EMAIL_AUTH_MODE_PLAIN,
            self::EMAIL_AUTH_MODE_CRAM_MD5 => self::EMAIL_AUTH_MODE_CRAM_MD5,
        ];
    }

    /**
     * @param $src string
     *
     * @return string
     */
    public function getTemplateSource($src)
    {
        if (0 === mb_strpos($src, 'Compo')) {
            $parser = $this->getContainer()->get('templating.name_parser');
            $locator = $this->getContainer()->get('templating.loader');
            $path = $locator->load($parser->parse($src));

            if ($path) {
                return $path->getContent();
            }
        }

        return $src;
    }

    /**
     * @return array
     */
    public function getEventsChoice()
    {
        $choice = [];

        foreach ($this->getDefaultEvents() as $event_key => $event) {
            $choice[$event['event']] = $event['event'];
        }

        return $choice;
    }

    /**
     * @param $event
     * @param $vars
     *
     * @throws \Exception
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     *
     * @return array
     */
    public function send($event, $vars)
    {
        /** @var NotificationEmail[] $notifications */
        $notifications = $this->getNotificationsEmail($event);

        $sites = $this->getContainer()->get('sonata.page.manager.site')->findAll();

        $vars['site'] = $sites[0];
        $vars['compo_core_settings'] = $this->getContainer()->get('compo_core.manager')->getSettings();
        $vars['compo_notification_email_settings'] = $this->getContainer()->get('sylius.settings_manager')->load(
            'compo_notification_email_settings'
        );

        $results = [];

        foreach ($notifications as $notification) {
            $recipients = $this->prepareEmails($notification->getRecipient(), $vars);

            foreach ($recipients as $email) {
                if (!$email) {
                    continue;
                }

                $subject = $this->renderTemplate($notification->getSubject(), $vars);
                $body = $this->renderTemplate($notification->getBody(), $vars);

                $sender = $notification->getSender();

                if (!$sender) {
                    $sender = $this->getDefaultSender();
                }

                $from = $sender->getUsername();

                if ('smtp' === mb_strtolower($sender->getTransport())) {
                    $transport = (new \Swift_SmtpTransport($sender->getHostname(), $sender->getPort()))
                        ->setUsername($sender->getUsername())
                        ->setPassword($sender->getPassword());
                    $transport->setAuthMode(mb_strtolower($sender->getAuthMode()));
                    $transport->setEncryption(mb_strtolower($sender->getEncryption()));
                    $mailer = new \Swift_Mailer($transport);
                } else {
                    //$transport = new \Swift_SendmailTransport();
                    $mailer = $this->getContainer()->get('mailer');
                }

                /** @var \Swift_Message $message */
                $message = $mailer->createMessage();

                $message->setSubject($subject)
                    ->setFrom($from, $sender->getName())
                    ->setTo($email)
                    ->setBody($body, 'text/html');

                $mailer->send($message);

                $results[] = [
                    'notification' => $notification,
                    'vars' => $vars,
                    'recipient' => $email,
                    'subject' => $subject,
                    'body' => $body,
                ];
            }
        }

        $notificationsSms = $this->getNotificationsSms($event);

        foreach ($notificationsSms as $notification) {
            $recipients = $this->prepareEmails($notification->getRecipient(), $vars);

            foreach ($recipients as $email) {
                if (!$email) {
                    continue;
                }

                $transport = new SmsRuTransport();

                $body = $this->renderTemplate($notification->getBody(), $vars);

                $sender = $notification->getSender();

                $transport->setUsername($sender->getUsername());
                $transport->setPassword($sender->getPassword());
                $transport->setSender($sender->getSender());

                $transport->send($email, $body);

                $results[] = [
                    'notification' => $notification,
                    'vars' => $vars,
                    'recipient' => $email,
                    'subject' => '',
                    'body' => $body,
                ];
            }
        }

        return $results;
    }

    /**
     * @param $event
     *
     * @return array|\Doctrine\Common\Persistence\ObjectRepository
     */
    public function getNotificationsEmail($event)
    {
        $notificationEmailRepository = $this->getContainer()->get('doctrine')->getRepository(
            'CompoNotificationBundle:NotificationEmail'
        );

        return $notificationEmailRepository->findBy(['event' => $event]);
    }

    /**
     * @param $str
     * @param $vars
     *
     * @throws \Exception
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     *
     * @return array
     */
    public function prepareEmails($str, $vars)
    {
        $str = $this->renderTemplate($str, $vars);

        return explode(',', $str);
    }

    /**
     * @param $template
     * @param $vars
     *
     * @throws \Exception
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     *
     * @return string
     */
    public function renderTemplate($template, $vars)
    {
        $twig = $this->getContainer()->get('twig');

        $template = $twig->createTemplate($template);

        return $template->render($vars);
    }

    /**
     * @return NotificationEmailAccount
     */
    public function getDefaultSender()
    {
        if (null === $this->defaultSender) {
            $settings = $this->getNotificationEmailSettings();

            $id = $settings->get('notification_email_account_default');

            if ($id) {
                $this->defaultSender = $this->getEntityManager()->getRepository(
                    NotificationEmailAccount::class
                )->find($id);
            } else {
                $this->defaultSender = $this->getEntityManager()->getRepository(
                    NotificationEmailAccount::class
                )->findOneBy([], ['id' => 'ASC']);
            }
        }

        return $this->defaultSender;
    }

    /**
     * @return \Sylius\Bundle\SettingsBundle\Model\SettingsInterface
     */
    public function getNotificationEmailSettings()
    {
        return $this->getContainer()->get('sylius.settings_manager')->load('compo_notification_email_settings');
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @param $event string
     *
     * @return NotificationSms[]
     */
    public function getNotificationsSms($event)
    {
        $notificationEmailRepository = $this->getContainer()->get('doctrine')->getRepository(
            'CompoNotificationBundle:NotificationSms'
        );

        return $notificationEmailRepository->findBy(['event' => $event]);
    }
}
