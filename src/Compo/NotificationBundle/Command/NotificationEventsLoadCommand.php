<?php

namespace Compo\NotificationBundle\Command;

use Compo\NotificationBundle\Entity\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class NotificationEventsLoadCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:notification:load')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $notificationManager = $container->get('compo_notification.manager.notification');
        $events = $notificationManager->getEvents();


        $em = $container->get('doctrine')->getManager();

        $notificationEmailRepository = $em->getRepository('CompoNotificationBundle:NotificationEmail');

        foreach ($events as $event_key => $event) {
            if ($event['type'] == 'email') {
                if (!$notificationEmailRepository->findBy(array('event' => $event['event']))) {
                    $emailEvent = new NotificationEmail();
                    $emailEvent->setEvent($event['event']);
                    $emailEvent->setBody($notificationManager->getTemplateSource($event['body']));
                    $emailEvent->setRecipient($notificationManager->getTemplateSource($event['recipient']));
                    $emailEvent->setSubject($notificationManager->getTemplateSource($event['subject']));
                    $emailEvent->setEnabled(true);
                    $em->persist($emailEvent);
                    $em->flush();
                }
            }
        }
    }
}
