<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\NotificationBundle\Command;

use Compo\NotificationBundle\Entity\NotificationEmail;
use Compo\NotificationBundle\Repository\NotificationEmailRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritdoc}
 */
class NotificationEventsLoadCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:notification:load')
            ->setDescription('Notifications load');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $notificationManager = $container->get('compo_notification.manager.notification');

        $events = $notificationManager->getDefaultEvents();

        $em = $container->get('doctrine')->getManager();

        /** @var NotificationEmailRepository $notificationEmailRepository */
        $notificationEmailRepository = $em->getRepository('CompoNotificationBundle:NotificationEmail');

        $translator = $this->getContainer()->get('translator');

        foreach ($events as $event_key => $event) {
            if ('email' === $event['type'] && !$notificationEmailRepository->findBy(['code' => $event['name']])) {
                $emailEvent = new NotificationEmail();
                $emailEvent->setName($translator->trans($event['name'], [], 'CompoNotificationBundle'));
                $emailEvent->setCode($event['name']);
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
