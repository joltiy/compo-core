<?php

namespace Compo\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallCommand
 *
 * @package Compo\CoreBundle\Command
 */
class InstallCommand extends BaseDeployCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('compo:core:install')
            ->setDescription('Install projects');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->runCacheClear(false);

        $output->writeln('runDoctrineCreateDatabase');

        $this->runDoctrineCreateDatabase();
        $this->runDoctrineSchemaUpdate();

        $this->runSyliusThemeAssetsInstall();

        //$this->runDoctrineMigrate();
        $this->runCreateAdmin();
        $this->runCreateSite();

        $this->runUpdateRoutes();
        $this->runCreateSnapshots();

        $this->runDoctrineFixturesLoadAppend();

        $this->runCacheWarmup();

        $this->runCommand("compo:notification:load");
    }


    /**
     * bin/console doctrine:schema:update --force
     * Выполняет создание БД
     *
     * @throws \Exception
     */
    public function runDoctrineCreateDatabase()
    {
        $this->runCommand(
            "doctrine:database:create",
            array(
                '--if-not-exists' => true
            )
        );
    }

    /**
     * Создание админа
     */
    public function runCreateAdmin()
    {
        $admin = $this->getContainer()->get('fos_user.user_manager')->findOneBy(
            array(
                'username' => 'admin'
            )
        );

        if (!$admin) {
            $this->runCommand(
                "fos:user:create",
                array(
                    '--super-admin' => true,
                    'username' => 'admin',
                    'email' => 'admin@admin.com',
                    'password' => 'admin'
                )
            );
        }
    }

    /**
     * Создание сайта
     */
    public function runCreateSite()
    {
        $sites = $this->getSites();

        if (!$sites) {
            $this->runCommand(
                "sonata:page:create-site",
                array(
                    'command' => 'sonata:page:create-site',
                    '--enabled' => true,
                    '--name' => 'localhost',
                    '--locale' => '-',
                    '--host' => 'localhost',
                    '--relativePath' => '/',
                    '--enabledFrom' => 'now',
                    '--enabledTo' => '+10 years',
                    '--default' => 'true',
                    '--no-interaction' => 'true',
                    '--no-confirmation' => 'true',
                )
            );
        }
    }
}