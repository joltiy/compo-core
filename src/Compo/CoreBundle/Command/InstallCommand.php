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
 * Class InstallCommand.
 */
class InstallCommand extends BaseDeployCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:core:install')
            ->setDescription('Install projects');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        //$this->runCacheClear(false);
        $this->runDoctrineCreateDatabase();
        $this->runDoctrineSchemaUpdate();
        //$this->runDoctrineMigrate();
        $this->runCreateAdmin();
        $this->runCreateSite();
        $this->runUpdateRoutes();
        $this->runCreateSnapshots();
        //$this->runDoctrineFixturesLoadAppend();
        //$this->runCommand('sonata:cache:flush-all');
        //$this->runCacheWarmup();
    }

    /**
     * bin/console doctrine:schema:update --force
     * Выполняет создание БД.
     *
     * @throws \Exception
     */
    public function runDoctrineCreateDatabase()
    {
        $this->runCommand(
            'doctrine:database:create',
            [
                '--if-not-exists' => true,
            ]
        );
    }

    /**
     * Создание админа.
     */
    public function runCreateAdmin()
    {
        $admin = $this->getContainer()->get('fos_user.user_manager')->findOneBy(
            [
                'username' => 'admin',
            ]
        );

        if (!$admin) {
            $this->runCommand(
                'fos:user:create',
                [
                    '--super-admin' => true,
                    'username' => 'admin',
                    'email' => 'admin@admin.com',
                    'password' => 'admin',
                ]
            );
        }
    }

    /**
     * Создание сайта.
     */
    public function runCreateSite()
    {
        $sites = $this->getSites();

        if (!$sites) {
            $this->runCommand(
                'sonata:page:create-site',
                [
                    'command' => 'sonata:page:create-site',
                    '--enabled' => true,
                    '--name' => 'WebSiteDemo',
                    '--locale' => '-',
                    '--host' => 'localhost',
                    '--relativePath' => '/',
                    '--enabledFrom' => 'now',
                    '--enabledTo' => '+10 years',
                    '--default' => 'true',
                    '--no-interaction' => 'true',
                    '--no-confirmation' => 'true',
                ]
            );
        }
    }
}
