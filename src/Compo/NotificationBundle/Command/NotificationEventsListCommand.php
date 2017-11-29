<?php

namespace Compo\NotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritDoc}
 */
class NotificationEventsListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:notification:list')
            ->setDescription('Notifications list');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $notificationManager = $container->get('compo_notification.manager.notification');
        $events = $notificationManager->getDefaultEvents();

        $table = new Table($output);
        $table->setHeaders(['name', 'event', 'recipient', 'subject', 'body', 'help', 'type']);

        foreach ($events as $event_key => $event) {
            $table->addRow(
                [
                    $event['name'],
                    $event['event'],
                    $event['recipient'],
                    $event['subject'],
                    $event['body'],
                    $event['help'],
                    $event['type'],
                ]
            );
        }

        $table->render();
    }
}
