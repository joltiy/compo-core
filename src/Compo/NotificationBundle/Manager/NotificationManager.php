<?php

namespace Compo\NotificationBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\NotificationBundle\Entity\NotificationEmail;

class NotificationManager
{
    use ContainerAwareTrait;

    public $events = array();

    public $default_sender;

    public function getEmailTransport() {
        return array(
            'smtp' => 'SMTP',
            'sendmail' => 'SendMail'
        );
    }

    public function getEmailEncryption() {
        return array(
            'tls' => 'TLS',
            'ssl' => 'SSL'
        );
    }

    public function getEmailAuthMode() {
        return array(
            'plain' => 'Plain',
            'login' => 'Login',
            'cram-md5' => 'Cram-MD5',
        );
    }

    /**
     *
     * @return object|\Sylius\Bundle\SettingsBundle\Model\SettingsInterface
     */
    public function getNotificationEmailSettings()
    {
        return $this->getContainer()->get('sylius.settings.manager')->load('compo_notification_email_settings');
    }


    public function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     *
     */
    public function getDefaultSender()
    {
        if (is_null($this->default_sender)) {
            $settings = $this->getNotificationEmailSettings();

            $id = $settings->get('notification_email_account_default');

            if ($id) {
                $this->default_sender = $this->getEntityManager()->getRepository('CompoNotificationBundle:NotificationEmailAccount')->find($id);
            } else {
                $this->default_sender = $this->getEntityManager()->getRepository('CompoNotificationBundle:NotificationEmailAccount')->findOneBy(array(), array('id' => 'ASC'));
            }
        }

        return $this->default_sender;
    }

    public function getTemplateSource($src) {

        if (strpos($src, 'Compo') === 0) {
            $parser = $this->getContainer()->get('templating.name_parser');
            $locator = $this->getContainer()->get('templating.loader');
            $path = $locator->load($parser->parse($src));
            if ($path) {
                return $path->getContent();
            } else {
                return $src;
            }
        } else {
            return $src;
        }
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    public function getEventsChoice()
    {
        $choice = array();

        foreach ($this->events as $event_key => $event) {
            $choice[$event_key] = $event_key;
        }

        return $choice;
    }

    /**
     * @param array $events
     */
    public function setEvents($events)
    {
        foreach ($events as $event_key => $event) {
            $this->events[$event['event']] = $event;
        }
    }

    public function getEvent($name) {
        return $this->events[$name];
    }

    public function getNotificationsEmail($event) {
        /*
        $events = array();

        foreach ($this->events as $event_key => $event_val) {
            if ($event_val['event'] == $event && $event_val['event'] == 'email') {
                $events[$event_key] = $event_val;
            }
        }

        return $events;
        */


        $notificationEmailRepository = $this->getContainer()->get('doctrine')->getRepository('CompoNotificationBundle:NotificationEmail');

        return $notificationEmailRepository->findBy(array('event' => $event));

    }

    public function send($event, $vars) {
        /** @var NotificationEmail[] $notifications */
        $notifications = $this->getNotificationsEmail($event);

        $vars['site'] = $this->getContainer()->get('sonata.seo.page')->getSite();
        $vars['compo_core_settings'] = $this->getContainer()->get('sylius.settings.manager')->load('compo_core_settings');

        dump($notifications);
dump($event);
        $results = array();

        foreach ($notifications as $notification) {
            $recipients = $this->prepareEmails($notification->getRecipient(), $vars);

            $mailer = $this->getContainer()->get('mailer');

            foreach ($recipients as $email) {
                if (!$email) {
                    continue;
                }

                /** @var \Swift_Message $message */
                $message = $mailer->createMessage();

                $subject = $this->renderTemplate($notification->getSubject(), $vars);
                $body = $this->renderTemplate($notification->getBody(), $vars);


                $sender = $notification->getSender();

                if (!$sender) {
                    $sender = $this->getDefaultSender();
                }


                if ($sender->getTransport() == 'smtp') {
                    $transport = (new \Swift_SmtpTransport($sender->getHostname(), $sender->getPort()))
                        ->setUsername($sender->getUsername())
                        ->setPassword($sender->getPassword())
                    ;
                    $transport->setAuthMode($sender->getAuthMode());
                    $transport->setEncryption($sender->getEncryption());
                } else {
                    $transport = new \Swift_SendmailTransport();
                }

                $mailer = new \Swift_Mailer($transport);


                $message->setSubject($subject)
                    ->setFrom($sender->getUsername(), $sender->getName())
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

        return $results;
    }

    public function prepareEmails($str, $vars) {
        $str = $this->renderTemplate($str, $vars);

        return explode(',', $str);
    }

    public function renderTemplate($template, $vars) {
        $twig = $this->getContainer()->get('twig');

        $template = $twig->createTemplate($template);

        return $template->render($vars);
    }

}