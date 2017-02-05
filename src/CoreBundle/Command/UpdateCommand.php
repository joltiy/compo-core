<?php

namespace Compo\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateCommand
 *
 * @package Compo\CoreBundle\Command
 */
class UpdateCommand extends BaseDeployCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('compo:update')
            ->setDescription('Update');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->runDoctrineMigrate();
        $this->runUpdateRoutes();
        $this->runCreateSnapshots();
        $this->runCacheWarmup();

        return 0;
    }
}