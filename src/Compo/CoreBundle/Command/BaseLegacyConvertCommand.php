<?php

namespace Compo\CoreBundle\Command;

use Compo\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * {@inheritDoc}
 */
class BaseLegacyConvertCommand extends ContainerAwareCommand
{
    use LockableTrait;

    /**
     * @var  SymfonyStyle
     */
    public $io;

    /**
     * @var OutputInterface
     */
    public $output;

    /**
     * @var InputInterface
     */
    public $input;

    /**
     * Подключение к старой БД
     *
     * @var \Doctrine\DBAL\Connection
     */
    public $oldConnection;

    /**
     * Изображения
     *
     * @var array
     */
    protected $media = array();

    /**
     * Лимит для импорта
     *
     * @var integer
     */
    protected $limit = 0;

    /**
     * Выборка от строки
     *
     * @var integer
     */
    protected $from = 0;

    /**
     * Потоки
     *
     * @var integer
     */
    protected $thread = 10;

    /**
     *
     * @var bool
     */
    protected $dryRun = false;

    /**
     * Удалить старые данные
     *
     * @var bool
     */
    protected $drop = false;

    /**
     * Получить путь для выполнения консольной команды.
     *
     * @return string
     */
    public function getConsoleBinCommand()
    {
        return 'php ' . $this->getProjectDir() . '/bin/console';
    }

    /**
     * Путь к директории проекта
     *
     * @return string
     */
    public function getProjectDir()
    {
        return dirname($this->getContainer()->get('kernel')->getRootDir()) . '/';
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    public function getMediaManager() {
        return $this->getContainer()->get('sonata.media.manager.media');
    }
    /**
     * @return int
     */
    public function getFrom(): int
    {
        return $this->from;
    }

    /**
     * @param int $from
     */
    public function setFrom(int $from)
    {
        $this->from = $from;
    }

    /**
     * @return bool
     */
    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    /**
     * @param bool $dryRun
     */
    public function setDryRun(bool $dryRun)
    {
        $this->dryRun = $dryRun;
    }

    /**
     * @return int
     */
    public function getThread(): int
    {
        return $this->thread;
    }

    /**
     * @param int $thread
     */
    public function setThread(int $thread)
    {
        $this->thread = $thread;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @param InputInterface $input
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * @return array
     */
    public function getMedia(): array
    {
        return $this->media;
    }

    /**
     * @param array $media
     */
    public function setMedia(array $media)
    {
        $this->media = $media;
    }

    /**
     * @param $id
     * @return null|Media
     */
    public function downloadMedia($id)
    {
        $media = null;

        if (isset($this->media[$id]) && $this->media[$id]['media_id']) {
            $container = $this->getContainer();

            $mediaManager = $container->get('sonata.media.manager.media');

            $media = $mediaManager->find($this->media[$id]['media_id']);
        }

        return $media;
    }

    /**
     * @param $name
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getCurrentRepository($name)
    {
        return $this->getEntityManager()->getRepository($name);
    }

    /**
     * @param $currentRepository \Doctrine\Common\Persistence\ObjectRepository
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function clearCurrent($currentRepository)
    {
        if ($this->isDrop()) {
            $this->getIo()->note('Clear: ' . $currentRepository->getClassName());

            /** @var EntityManager $em */
            $em = $this->getEntityManager();

            $em->getFilters()->disable('softdeleteable');


            $q = $em->createQuery('delete from ' . $currentRepository->getClassName() . ' m');
            $numDeleted = $q->execute();

            $this->getIo()->note('Clear: ' . $numDeleted);

            $em->getFilters()->enable('softdeleteable');

            $em->flush();
        }
    }

    /**
     * @return bool
     */
    public function isDrop(): bool
    {
        return $this->drop;
    }

    /**
     * @param bool $drop
     */
    public function setDrop(bool $drop)
    {
        $this->drop = $drop;
    }

    /**
     * @param string $prefix
     */
    public function writeln($prefix = '')
    {
        $this->output->writeln($prefix);
    }

    /**
     * @param $newItem
     */
    public function changeIdGenerator($newItem)
    {
        $metadata = $this->getEntityManager()->getClassMetadata(get_class($newItem));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    }

    /**
     *
     */
    protected function processMedia()
    {
        $this->getIo()->section('Process Media. Load old');

        $db_pics = $this->getOldData('db_pics');


        foreach ($db_pics as $db_pic) {

            $this->media[$db_pic['id']] = $db_pic;
        }

        $this->writelnMemory();

        unset($db_pics);

        $media_isset = array();

        $this->getIo()->note('Process Media. Load current');

        $media = $this->getEntityManager()->getConnection()->fetchAll('SELECT * FROM `media__media` ORDER BY id ASC ');
        $this->getIo()->note('Count: ' . count($media));


        foreach ($media as $item) {
            $media_isset[$item['name']] = $item['id'];
        }

        $this->writelnMemory();

        unset($media);

        $this->getIo()->note('Process Media. Compare');

        foreach ($this->media as $id => $item_data) {

            $media_key = $item_data['id'] . '.' . $item_data['type'];

            $item = null;

            if (isset($media_isset[$media_key])) {
                $this->media[$id]['media_id'] = $media_isset[$media_key];
            } else {
                $this->media[$id]['media_id'] = false;
            }
        }


        $this->writelnMemory();

        $this->getIo()->success('Load Media Finish');
    }

    /**
     * @return SymfonyStyle
     */
    public function getIo(): SymfonyStyle
    {
        return $this->io;
    }

    /**
     * @param SymfonyStyle $io
     */
    public function setIo(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    /**
     * @param $table
     * @return array
     */
    public function getOldData($table)
    {
        $this->getIo()->note('Load from old table: ' . $table);

        $query = 'SELECT * FROM `' . $table . '` ORDER BY id ASC';

        if ($this->limit && $this->from) {
            $query .= ' LIMIT ' . $this->limit . ',' . $this->from;
        }

        $oldData = $this->getOldConnection()->fetchAll($query);

        $this->getIo()->note('Count: ' . count($oldData));

        return $oldData;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getOldConnection(): \Doctrine\DBAL\Connection
    {
        return $this->oldConnection;
    }

    /**
     * @param \Doctrine\DBAL\Connection $oldConnection
     */
    public function setOldConnection(\Doctrine\DBAL\Connection $oldConnection)
    {
        $this->oldConnection = $oldConnection;
    }

    /**
     */
    protected function writelnMemory()
    {
        $this->getIo()->writeln('');
        $this->getIo()->note('Memmory: ' . number_format(memory_get_usage(), 0, ',', ' ') . ' B');
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }
    public function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        ini_set('memory_limit', -1);
        gc_enable();

        $this
            ->setName('compo:legacy:convert:database:base')
            ->setDescription('Convert from old databas, base command')
            ->addOption(
                'drop',
                null,
                InputOption::VALUE_REQUIRED,
                'Drop old data',
                false
            )->addOption(
                'dry-run',
                null,
                InputOption::VALUE_REQUIRED,
                'Dry-run',
                false
            )
            ->addOption(
                'host',
                null,
                InputOption::VALUE_REQUIRED,
                'Databse old host'
            )->addOption(
                'port',
                null,
                InputOption::VALUE_REQUIRED,
                'Databse old port',
                3306
            )->addOption(
                'login',
                null,
                InputOption::VALUE_REQUIRED,
                'Databse old login'
            )->addOption(
                'password',
                null,
                InputOption::VALUE_REQUIRED,
                'Databse old password'
            )->addOption(
                'database',
                null,
                InputOption::VALUE_REQUIRED,
                'Databse old name'
            )->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                'Limit items',
                0
            )->addOption(
                'from',
                null,
                InputOption::VALUE_REQUIRED,
                'From item',
                0
            )->addOption(
                'thread',
                null,
                InputOption::VALUE_REQUIRED,
                'Count thread',
                10
            )->addOption(
                'tables',
                null,
                InputOption::VALUE_REQUIRED,
                'Tables',
                false
            );

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setIo(new SymfonyStyle($input, $output));
        $this->setInput($input);
        $this->setOutput($output);

        $io = $this->getIo();


        if (!$this->lock()) {
            $io->error('The command is already running in another process.');

            return;
        }

        $io->title($this->getName());

        $this->getEntityManager()->getConnection()->getConfiguration()->setSQLLogger();

        $this->setFrom($input->getOption('from'));
        $this->setDrop($input->getOption('drop'));
        $this->setLimit($input->getOption('limit'));
        $this->setThread($input->getOption('thread'));
        $this->setDryRun($input->getOption('dry-run'));

        $this->createOldConnection(
            array(
                'host' => $input->getOption('host'),
                'login' => $input->getOption('login'),
                'password' => $input->getOption('password'),
                'database' => $input->getOption('database')
            )
        );


        $this->startProcess();

        $this->release();
    }

    /**
     *
     */
    protected function startProcess()
    {

    }

    /**
     * Создать подключение к старой БД
     *
     * @param array $config
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createOldConnection(array $config = array())
    {
        $this->getIo()->section('Create old connection');

        /** @var \Doctrine\Bundle\DoctrineBundle\ConnectionFactory $connectionFactory */
        $connectionFactory = $this->getContainer()->get('doctrine.dbal.connection_factory');

        $oldConnection = $connectionFactory->createConnection(
            array('pdo' => new \PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['database'], $config['login'], $config['password']))
        );

        $oldConnection->connect();

        $oldConnection->query('SET NAMES utf8');
        $oldConnection->getConfiguration()->setSQLLogger();

        $this->setOldConnection($oldConnection);

        $this->getIo()->success('Create old connection');
    }


}
