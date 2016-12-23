<?php

namespace Compo\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * {@inheritDoc}
 */
class ConvertAllImagesFromOldDatabaseCommand extends ContainerAwareCommand
{
    /**
     * @var \Doctrine\Common\Persistence\AbstractManagerRegistry
     */
    public $em;

    /**
     * @var OutputInterface
     */
    public $output;

    public $database_name = 'dlyavann';

    public $features = array();

    /**
     * @var \Doctrine\DBAL\Connection
     */
    public $oldConnection;

    public $oldMediaPath = 'http://www.dlyavann.ru/dbpics/';

    public $oldFilesPath = 'http://www.dlyavann.ru/files/';


    public $dbpics = array();

    public $limit = false;

    public $data = array(
        'Currency' => array(),
        'ProductAvailability' => array(),
        'Supplier' => array(),
        'Country' => array(),
        'Manufacture' => array(),
        'ManufactureCollection' => array(),
        'Catalog' => array(),
    );

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        ini_set('memory_limit', -1);

        $this
            ->setName('compo:convert_all_images_from_old_database')
            ->setDescription('Convert all images from old database')
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
                'Limit',
                false
            )->addOption(
                'oldMediaPath',
                null,
                InputOption::VALUE_REQUIRED,
                'oldMediaPath',
                $this->oldMediaPath
            );

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

        $this->em = $em;

        $oldHost = $input->getOption('host');
        //$oldPort = $input->getOption('port');
        $oldLogin = $input->getOption('login');
        $oldPassword = $input->getOption('password');
        $oldDatabase = $input->getOption('database');
        $this->limit = $input->getOption('limit');
        $this->oldMediaPath = $input->getOption('oldMediaPath');

        /** @var \Doctrine\Bundle\DoctrineBundle\ConnectionFactory $connectionFactory */
        $connectionFactory = $container->get('doctrine.dbal.connection_factory');
        $oldConnection = $connectionFactory->createConnection(
            array('pdo' => new \PDO("mysql:host=$oldHost;dbname=$oldDatabase", $oldLogin, $oldPassword))
        );

        $oldConnection->connect();

        $oldConnection->query('SET NAMES utf8');

        $this->em = $em;
        $this->output = $output;
        $this->oldConnection = $oldConnection;

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->oldConnection->getConfiguration()->setSQLLogger(null);

        gc_enable();
        $this->process();
    }

    public function process()
    {
        $this->processMedia();
    }


    public function processMedia()
    {
        $container = $this->getContainer();

        $db_pics = $this->oldConnection->fetchAll('SELECT * FROM db_pics ORDER BY id ASC');

        foreach ($db_pics as $db_pic) {
            $this->dbpics[$db_pic['id']] = $db_pic;
        }

        unset($db_pics);

        $i = 1;


        $kernel = $container->get('kernel');

        $root_dir = $kernel->getRootDir();

        $console = 'php ' . $root_dir . '/console';


        $media_isset = array();

        $media = $this->em->getConnection()->fetchAll('SELECT * FROM `media__media` ORDER BY id ASC ');


        foreach ($media as $item) {
            $media_isset[$item['name']] = $item['id'];
        }

        unset($media);

        $queue = array();

        foreach ($this->dbpics as $id => $item_data) {


            $this->output->writeln('Media' . '. ' . $i . ': ' . $item_data['id'] . '.' . $item_data['type']);

            $this->output->writeln('Memmory: ' . number_format((memory_get_usage()), 0, ',', ' ') . ' B');


            $item = null;

            if (isset($media_isset[$item_data['id'] . '.' . $item_data['type']])) {

                $this->dbpics[$id]['media_id'] = $media_isset[$item_data['id'] . '.' . $item_data['type']];
            } else {

                $command = $console . ' compo:convert_image_from_old_database --no-debug --name=' . $item_data['id'] . '.' . $item_data['type'] . ' --path=' . $this->oldMediaPath . $item_data['id'] . '.' . $item_data['type'];

                $this->output->writeln($command);

                $process = new Process($command);

                $process->start();

                $queue[] = $process;


                while (count($queue) > 10) {
                    foreach ($queue as $queue_key => $queue_item) {
                        if (!$queue_item->isRunning()) {
                            unset($queue[$queue_key]);
                        }
                    }

                    usleep(100);
                }

                foreach ($queue as $queue_key => $queue_item) {
                    if (!$queue_item->isRunning()) {
                        unset($queue[$queue_key]);
                    }
                }
            }

            $i++;
        }


        while (count($queue) > 0) {
            foreach ($queue as $queue_key => $queue_item) {
                if (!$queue_item->isRunning()) {
                    unset($queue[$queue_key]);
                }
            }

            usleep(100);
        }


    }

}
