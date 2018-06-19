<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateCommand.
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        //$this->runCacheClear(false);
        //$this->runDoctrineMigrate();
        $this->runDoctrineSchemaUpdate();
        $this->runUpdateRoutes();
        $this->runCreateSnapshots();
        //$this->runDoctrineFixturesLoadAppend();
        $this->runCommand('compo:notification:load');
        $this->runCommand('compo:seo:page:load');

        //
        //$this->runCacheWarmup();

        return null;
    }
}
