<?php

namespace Compo\CoreBundle\Command;

use Compo\Sonata\PageBundle\Entity\Page;
use Compo\Sonata\PageBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BaseDeployCommand
 *
 * @package Compo\CoreBundle\Command
 */
class BaseDeployCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    public $output;

    /**
     * Выполняет миграции
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function runDoctrineMigrate()
    {
        $this->runCommand("doctrine:migrations:migrate", array(
            '--no-interaction' => 1
        ));
    }

    /**
     * Выполняет консольную команду
     *
     * @param       $command    string Команда
     * @param array $arrayInput Аргументы
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function runCommand($command, $arrayInput = array())
    {
        $application = $this->getApplication();

        $command = $application->find($command);

        $arrayInput = new ArrayInput($arrayInput);

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
     * Обновляет маршрутизацию
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function runUpdateRoutes()
    {
        $sites = $this->getSites();

        foreach ($sites as $site) {
            /** @var $site Site */
            $this->runCommand("sonata:page:update-core-routes", array(
                '--site' => array($site->getId())
            ));
        }
    }

    /**
     * Возращает список сайтов
     *
     * @return array
     */
    public function getSites()
    {
        return $this->getContainer()->get('sonata.page.manager.site')->findAll();
    }

    /**
     * Публикует страницы
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function runCreateSnapshots()
    {
        $pages = $this->getContainer()->get('sonata.page.manager.page')->findBy(array(
            'edited' => 1
        ));

        foreach ($pages as $item) {
            /** @var $item Page */
            $this->getContainer()->get('sonata.notification.backend.runtime')->createAndPublish('sonata.page.create_snapshot', array(
                'pageId' => $item->getId(),
                'mode' => 'sync',
            ));
        }
    }

    /**
     * Очистка и прогрев кеша
     *
     * @param bool $warmup
     */
    public function runCacheClear($warmup = true)
    {
        if ($warmup) {
            $this->runCommand("cache:clear");
        } else {
            $this->runCommand("cache:clear", array(
                '--no-warmup' => 1,
            ));
        }
    }

    public function runSyliusThemeAssetsInstall()
    {
        $this->runCommand("sylius:theme:assets:install", array(
            '--symlink' => true,
            '--relative' => true
        ));
    }

    public function runDoctrineSchemaUpdate()
    {
        $this->runCommand("doctrine:schema:update", array(
            '--force' => true
        ));
    }

    public function runDoctrineFixturesLoadAppend()
    {
        $this->runCommand("doctrine:fixtures:load", array(
            '--append' => true
        ));
    }

    /**
     * Очистка и прогрев кеша
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function runCacheWarmup()
    {
        $this->runCommand("cache:warmup");
    }

    /**
     * Удаляет таблицы БД
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function runSchemaDrop()
    {
        $this->runCommand("doctrine:schema:drop", array(
            '--no-interaction' => 1,
            '--force' => 1,
            '--quiet' => 1,
            '--no-debug' => 1,
            '--full-database' => 1
        ));
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('compo:base:deploy')
            ->setDescription('Dummy');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setOutput($output);
    }
}