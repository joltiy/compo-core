<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Command;

use Compo\Sonata\PageBundle\Entity\Page;
use Compo\Sonata\PageBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BaseDeployCommand.
 */
class BaseDeployCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    public $output;

    /**
     * Выполняет миграции.
     *
     * @throws \Exception
     */
    public function runDoctrineMigrate()
    {
        $this->runCommand(
            'doctrine:migrations:migrate',
            [
                '--no-interaction' => 1,
            ]
        );
    }

    /**
     * Выполняет консольную команду.
     *
     * @param       $commandName string Команда
     * @param array $args        Аргументы
     *
     * @throws \Exception
     */
    public function runCommand($commandName, array $args = [])
    {
        $application = $this->getApplication();

        $command = $application->find($commandName);

        $arrayInput = new ArrayInput($args);

        $arrayInput->setInteractive(false);

        $command->run($arrayInput, $this->getOutput());
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * Обновляет маршрутизацию.
     *
     * @throws \Exception
     */
    public function runUpdateRoutes()
    {
        $sites = $this->getSites();

        foreach ($sites as $site) {
            /* @var $site Site */
            $this->runCommand(
                'sonata:page:update-core-routes',
                [
                    '--site' => [$site->getId()],
                ]
            );
        }
    }

    /**
     * Возращает список сайтов.
     *
     * @return array
     */
    public function getSites()
    {
        return $this->getContainer()->get('sonata.page.manager.site')->findAll();
    }

    /**
     * Публикует страницы.
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function runCreateSnapshots()
    {
        $pages = $this->getContainer()->get('sonata.page.manager.page')->findBy(
            [
                'edited' => 1,
            ]
        );

        foreach ($pages as $item) {
            /* @var $item Page */
            $this->getContainer()->get('sonata.notification.backend')->createAndPublish(
                'sonata.page.create_snapshot',
                [
                    'pageId' => $item->getId(),
                    'mode' => 'sync',
                ]
            );
        }
    }

    /**
     * Очистка и прогрев кеша.
     *
     * @param bool $warmup
     *
     * @throws \Exception
     */
    public function runCacheClear($warmup = true)
    {
        if ($warmup) {
            $this->runCommand('cache:clear');
        } else {
            $this->runCommand(
                'cache:clear',
                [
                    '--no-warmup' => 1,
                ]
            );
        }
    }

    /**
     * @throws \Exception
     */
    public function runAsseticDump()
    {
        $this->runCommand(
            'assetic:dump',
            [
                '--env' => 'dev',
            ]
        );

        $this->runCommand(
            'assetic:dump',
            [
                '--env' => 'prod',
                '--no-debug',
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function runSyliusThemeAssetsInstall()
    {
        //$cache_dir = $this->getContainer()->getParameter('kernel.cache_dir');

        //mkdir($cache_dir . '/jms_diextra/metadata', 0777, true);

        $this->runCommand(
            'sylius:theme:assets:install',
            [
                '--symlink' => true,
                '--relative' => true,
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function runDoctrineSchemaUpdate()
    {
        $this->runCommand(
            'doctrine:schema:update',
            [
                '--force' => true,
                '--dump-sql' => true,
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function runDoctrineFixturesLoadAppend()
    {
        $this->runCommand(
            'doctrine:fixtures:load',
            [
                '--append' => true,
            ]
        );
    }

    /**
     * Очистка и прогрев кеша.
     *
     * @throws \Exception
     */
    public function runCacheWarmup()
    {
        $this->runCommand('cache:warmup');
        $this->runCommand('cache:warmup');
    }

    /**
     * Удаляет таблицы БД.
     *
     * @throws \Exception
     */
    public function runSchemaDrop()
    {
        $this->runCommand(
            'doctrine:schema:drop',
            [
                '--no-interaction' => 1,
                '--force' => 1,
                '--quiet' => 1,
                '--no-debug' => 1,
                '--full-database' => 1,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:core:base-deploy')
            ->setDescription('Base deploy (dummy)');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setOutput($output);
    }
}
