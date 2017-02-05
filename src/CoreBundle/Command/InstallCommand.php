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
     *
     */
    protected function configure()
    {
        $this
            ->setName('compo:install')
            ->setDescription('Install');
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

        $this->runCacheClear(false);

        $output->writeln('runDoctrineCreateDatabase');

        $this->runDoctrineCreateDatabase();

        $this->runDoctrineMigrate();
        $this->runCreateAdmin();
        $this->runCreateSite();

        $this->runUpdateRoutes();
        $this->runCreateSnapshots();

        $this->runCacheClear();

        return 0;
    }

    /**
     * Выполняет создание БД
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function runDoctrineCreateDatabase()
    {
        $this->runCommand("doctrine:database:create", array(
            '--if-not-exists' => 1
        ));
    }

    /**
     * Создание админа
     */
    public function runCreateAdmin()
    {
        $admin = $this->getContainer()->get('fos_user.user_manager')->findOneBy(array(
            'username' => 'admin'
        ));

        if (!$admin) {
            $this->runCommand("fos:user:create", array(
                '--super-admin' => true,
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => 'admin'
            ));
        }
    }

    /**
     * Создание сайта
     */
    public function runCreateSite()
    {
        $sites = $this->getSites();

        if (!$sites) {
            $this->runCommand("sonata:page:create-site", array(
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
            ));
        }
    }
}