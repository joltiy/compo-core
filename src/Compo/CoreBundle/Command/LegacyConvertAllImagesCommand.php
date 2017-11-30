<?php

namespace Compo\CoreBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

/**
 * {@inheritdoc}
 */
class LegacyConvertAllImagesCommand extends BaseLegacyConvertCommand
{
    /**
     * Путь к файлам старой БД.
     *
     * @var string
     */
    public $oldMediaPath = '/home/compodev24/www/keram/old/dbpics/';

    /**
     * Изображения из старой БД.
     *
     * @var array
     */
    public $dbpics = array();

    /**
     * Изображения из текущей БД.
     *
     * @var array
     */
    public $mediaIsset = array();

    /**
     * @return array
     */
    public function getDbpics(): array
    {
        return $this->dbpics;
    }

    /**
     * @param array $dbpics
     */
    public function setDbpics(array $dbpics)
    {
        $this->dbpics = $dbpics;
    }

    /**
     * Процесс конвертации изображений.
     */
    public function startProcess()
    {
        $this->setOldMediaPath($this->getInput()->getOption('oldMediaPath'));

        $this->getIo()->section('Start process');

        $this->processMedia();

        $this->loadDbPics();
        $this->loadMediaIsset();

        $this->processMediaLoad();
    }

    /**
     * Загрузка старых изображений.
     */
    public function loadDbPics()
    {
        $this->getIo()->section('Load old media');

        $db_pics = $this->getOldData('db_pics');

        foreach ($db_pics as $db_pic) {
            $this->dbpics[$db_pic['id']] = $db_pic;
        }

        $this->getIo()->success('Load old media');
    }

    /**
     * Загрузить текущие изображения.
     */
    public function loadMediaIsset()
    {
        $this->getIo()->section('Load current media');

        $media = $this->getEntityManager()->getConnection()->fetchAll('SELECT * FROM `media__media` ORDER BY id ASC ');

        $this->getIo()->note('Count: ' . count($media));

        foreach ($media as $item) {
            $this->mediaIsset[$item['name']] = $item['id'];
        }

        $this->getIo()->success('Load current media');
    }

    /**
     * Загрузка и конвертация изображений.
     */
    public function processMediaLoad()
    {
        $this->getIo()->section('Load new media process');

        $queue = array();

        $console = $this->getConsoleBinCommand();

        $this->getIo()->progressStart(count($this->dbpics));

        foreach ($this->dbpics as $id => $item_data) {
            $media_key = $item_data['id'] . '.' . $item_data['type'];

            $mediaIsset = isset($this->mediaIsset[$media_key]);

            if ($mediaIsset && !$this->isDrop()) {
                $this->dbpics[$id]['media_id'] = $this->mediaIsset[$media_key];

                $this->getIo()->progressAdvance();
            } else {
                if ($mediaIsset) {
                    $this->dbpics[$id]['media_id'] = $this->mediaIsset[$media_key];

                    $command = $console . ' compo:legacy:convert:image --dry-run=' . (int) $this->isDryRun() . ' --name=' . $media_key . ' --path=' . $this->getOldMediaPath() . $media_key . ' --id=' . $this->dbpics[$id]['media_id'];
                } else {
                    $command = $console . ' compo:legacy:convert:image --dry-run=' . (int) $this->isDryRun() . ' --name=' . $media_key . ' --path=' . $this->getOldMediaPath() . $media_key;
                }

                $process = new Process($command);

                //$process->start();

                $queue[] = $process;
            }
        }

        $queue_running = array();

        while (count($queue) > 0) {
            while (count($queue_running) < $this->getThread()) {
                /** @var Process $queue_item */
                $queue_item = array_shift($queue);
                $queue_item->start();

                $queue_running[] = $queue_item;
            }

            foreach ($queue_running as $queue_key => $queue_item) {
                if (!$queue_item->isRunning()) {
                    unset($queue_running[$queue_key]);
                    $this->getIo()->progressAdvance();
                    $this->getOutput()->writeln('');
                    $this->getOutput()->writeln($queue_item->getCommandLine());
                }
            }
        }

        while (count($queue_running) > 0) {
            foreach ($queue_running as $queue_key => $queue_item) {
                if (!$queue_item->isRunning()) {
                    unset($queue_running[$queue_key]);
                    $this->getIo()->progressAdvance();
                    $this->getOutput()->writeln('');
                    $this->getOutput()->writeln($queue_item->getCommandLine());
                }
            }
        }

        $this->getIo()->progressFinish();

        $this->getIo()->success('Load ok');
    }

    /**
     * @return string
     */
    public function getOldMediaPath(): string
    {
        return $this->oldMediaPath;
    }

    /**
     * @param string $oldMediaPath
     */
    public function setOldMediaPath(string $oldMediaPath)
    {
        $this->oldMediaPath = $oldMediaPath;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('compo:legacy:convert:all-images')
            ->setDescription('Convert all images from old database')
            ->addOption(
                'oldMediaPath',
                null,
                InputOption::VALUE_REQUIRED,
                'Media images path',
                $this->oldMediaPath
            );
    }
}
