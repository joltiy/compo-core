<?php

namespace Compo\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestMailCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:test_mail_command')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mailer = $this->getContainer()->get('mailer');

        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('jivoy1988@gmail.com')
            ->setTo('jivoy1988@gmail.com')
            ->setBody(
                '123',
                'text/html'
            );

        $mailer->send($message);


    }
}
