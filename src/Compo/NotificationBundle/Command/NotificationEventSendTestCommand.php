<?php

namespace Compo\NotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class NotificationEventSendTestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:notification:send:test')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $notificationManager = $this->getContainer()->get('compo_notification.manager.notification');

        $event = $notificationManager->getEvent('compo_notification_test');
        $vars = array('test' => 'test value');

        $results = $notificationManager->send($event['event'], $vars);

        $table = new Table($output);
        $table->setHeaders(array('event', 'description', 'subject', 'body', 'help'));
        $table->addRow(array($event['event'], $event['description'], $event['subject'], $event['body'], $event['help']));
        $table->render();

        $output->writeln('<comment>Vars: </comment>' . json_encode($vars));

        $output->writeln('<info>Results:</info>');

        foreach ($results as $result) {
            $output->writeln(json_encode($result));
        }

        $output->writeln('Notification event "<comment>' . $event['event'] . '</comment>" send <info>success</info>!');
    }
}
