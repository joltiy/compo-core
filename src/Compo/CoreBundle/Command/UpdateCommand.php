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
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:core:update')
            ->setDescription('Update project');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     * @throws \Exception
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->runCacheClear(false);

        //$this->runDoctrineMigrate();

        $this->runDoctrineSchemaUpdate();
        //$this->runSyliusThemeAssetsInstall();

        $this->runUpdateRoutes();
        $this->runCreateSnapshots();

        $this->runDoctrineFixturesLoadAppend();

        $this->runCommand('compo:notification:load');

        $this->runCacheWarmup();


        return 0;
    }
}