<?php

namespace Compo\NotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class NotificationEventsListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:notification:list')
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


        $table = new Table($output);
        $table->setHeaders(array('event', 'description', 'recipient', 'subject', 'body', 'help', 'type'));

        foreach ($events as $event_key => $event) {
            $table->addRow(array(
                $event['event'],
                $event['description'],
                $event['recipient'],
                $event['subject'],
                $event['body'],
                $event['help'],
                $event['type']
            ));
        }

        $table->render();
    }
}
