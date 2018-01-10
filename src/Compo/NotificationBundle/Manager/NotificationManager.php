<?php

namespace Compo\NotificationBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\NotificationBundle\Entity\NotificationEmail;
use Compo\NotificationBundle\Entity\NotificationEmailAccount;
use Compo\NotificationBundle\Entity\NotificationSms;
use Compo\NotificationBundle\Sms\SmsRuTransport;

class NotificationManager
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    public $defaultEvents = array();

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
        $events = array();

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
        return array(
            'SMTP' => 'smtp',
            'SendMail' => 'sendmail',
        );
    }

    /**
     * @return array
     */
    public function getSmsTransport()
    {
        return array(
            'sms.ru' => 'sms',
        );
    }

    /**
     * @return array
     */
    public function getEmailEncryption()
    {
        return array(
            'TLS' => 'tls',
            'SSL' => 'ssl',
        );
    }

    /**
     * @return array
     */
    public function getEmailAuthMode()
    {
        return array(
            'Plain' => 'plain',
            'Login' => 'login',
            'Cram-MD5' => 'cram-md5',
        );
    }

    /**
     * @param $src string
     *
     * @return string
     */
    public function getTemplateSource($src)
    {
        if (0 === strpos($src, 'Compo')) {
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
        $choice = array();

        foreach ($this->getDefaultEvents() as $event_key => $event) {
            $choice[$event['event']] = $event['event'];
        }

        return $choice;
    }

    /**
     * @param $event
     * @param $vars
     *
     * @return array
     *
     * @throws \Exception
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     */
    public function send($event, $vars)
    {
        /** @var NotificationEmail[] $notifications */
        $notifications = $this->getNotificationsEmail($event);

        $sites = $this->getContainer()->get('sonata.page.manager.site')->findAll();

        $vars['site'] = $sites[0];
        $vars['compo_core_settings'] = $this->getContainer()->get('sylius.settings_manager')->load(
            'compo_core_settings'
        );
        $vars['compo_notification_email_settings'] = $this->getContainer()->get('sylius.settings_manager')->load(
            'compo_notification_email_settings'
        );

        $results = array();

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

                if ('smtp' === strtolower($sender->getTransport())) {
                    $transport = (new \Swift_SmtpTransport($sender->getHostname(), $sender->getPort()))
                        ->setUsername($sender->getUsername())
                        ->setPassword($sender->getPassword());
                    $transport->setAuthMode(strtolower($sender->getAuthMode()));
                    $transport->setEncryption(strtolower($sender->getEncryption()));
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

                $results[] = array(
                    'notification' => $notification,
                    'vars' => $vars,
                    'recipient' => $email,
                    'subject' => $subject,
                    'body' => $body,
                );
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

                $results[] = array(
                    'notification' => $notification,
                    'vars' => $vars,
                    'recipient' => $email,
                    'subject' => '',
                    'body' => $body,
                );
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

        return $notificationEmailRepository->findBy(array('event' => $event));
    }

    /**
     * @param $str
     * @param $vars
     *
     * @return array
     *
     * @throws \Exception
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
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
     * @return string
     *
     * @throws \Exception
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     */
    public function renderTemplate($template, $vars)
    {
        $twig = $this->getContainer()->get('twig');

        $template = $twig->createTemplate($template);

        return $template->render($vars);
    }

    public function getDefaultSender()
    {
        if (null === $this->defaultSender) {
            $settings = $this->getNotificationEmailSettings();

            $id = $settings->get('notification_email_account_default');

            if ($id) {
                $this->defaultSender = $this->getEntityManager()->getRepository(
                    'CompoNotificationBundle:NotificationEmailAccount'
                )->find($id);
            } else {
                $this->defaultSender = $this->getEntityManager()->getRepository(
                    'CompoNotificationBundle:NotificationEmailAccount'
                )->findOneBy(array(), array('id' => 'ASC'));
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

        return $notificationEmailRepository->findBy(array('event' => $event));
    }
}
