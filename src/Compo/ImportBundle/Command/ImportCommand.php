<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ImportBundle\Command;

use Compo\ImportBundle\Entity\ImportLog;
use Compo\ImportBundle\Entity\UploadFile;
use Compo\ImportBundle\Loaders\FileLoaderInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class ImportCommand.
 */
class ImportCommand extends ContainerAwareCommand
{
    /** @var EntityManager $this ->em */
    protected $em;

    /**
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        return $this->em;
    }

    /**
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em): void
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:import')
            ->setDescription('Import data to sonata from CSV')
            ->addArgument('csv_file', InputArgument::REQUIRED, 'id UploadFile entity')
            ->addArgument('admin_code', InputArgument::REQUIRED, 'code to sonata admin bundle')
            ->addArgument('encode', InputArgument::OPTIONAL, 'file encode')
            ->addArgument('file_loader', InputArgument::OPTIONAL, 'number of loader class')
            ->addOption(
                'dry-run',
                'dr',
                InputOption::VALUE_NONE,
                'Dry run'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batchSize = 100;
        $logs = [];

        $isDryRun = $input->getOption('dry-run');

        $uploadFileId = $input->getArgument('csv_file');
        $adminCode = $input->getArgument('admin_code');
        $encode = mb_strtolower($input->getArgument('encode'));
        $fileLoaderId = $input->getArgument('file_loader');

        $container = $this->getContainer();

        $doctrine = $container->get('doctrine');

        /** @var EntityManager $em */
        $em = $doctrine->getManager();

        $this->setEm($em);

        /** @var UploadFile $uploadFile */
        $uploadFile = $em->getRepository(UploadFile::class)->find($uploadFileId);

        if (null === $uploadFile) {
            throw new \Exception('Upload file not found');
        }

        $fileLoaders = $container->getParameter('compo_import.class_loaders');

        $fileLoader = null;

        if (isset($fileLoaders[$fileLoaderId]['class'])) {
            $fileLoader = $fileLoaders[$fileLoaderId]['class'];
        }

        if (!class_exists($fileLoader)) {
            $uploadFile->setStatus(UploadFile::STATUS_ERROR);
            $uploadFile->setMessage('class_loader not found');

            $em->flush($uploadFile);

            return;
        }

        $fileLoader = new $fileLoader();

        if (!$fileLoader instanceof FileLoaderInterface) {
            $uploadFile->setStatus(UploadFile::STATUS_ERROR);
            $uploadFile->setMessage('class_loader must be instanceof "FileLoaderInterface"');

            $em->flush($uploadFile);

            return;
        }

        $fileLoader->setContainer($this->getContainer());

        try {
            $fileLoader->setFile(new File($uploadFile->getFile()));

            $pool = $container->get('sonata.admin.pool');

            /** @var \Compo\Sonata\AdminBundle\Admin\AbstractAdmin $instance */
            $instance = $pool->getInstance($adminCode);

            $entityClass = $instance->getClass();

            $meta = $em->getClassMetadata($entityClass);

            $identifier = $meta->getSingleIdentifierFieldName();

            $exportFields = $instance->getExportFields();

            $validator = $container->get('validator');

            $firstLine = [];

            $exportFieldsAsString = [];

            foreach ($exportFields as $exportFieldKey => $exportField) {
                if (false !== mb_strpos($exportField, 'ExportAsString')) {
                    $exportFieldsAsString[$exportFieldKey] = $exportFieldKey;
                    $exportFields[$exportFieldKey] = str_replace('ExportAsString', '', $exportField);
                }
            }

            /** @var array $iterator */
            $iterator = $fileLoader->getIteration();

            $headers = [];
            $headersRevert = [];
            $methods = [];

            $associationMappings = $meta->associationMappings;

            $fieldsMappings = $meta->fieldMappings;

            foreach ($iterator as $line => $dataRaw) {
                $output->writeln($line);

                if (0 === $line) {
                    $firstLine = $dataRaw;
                    $output->writeln($firstLine);

                    foreach ($exportFields as $key => $name) {
                        $data_key = $key;

                        if (!\in_array($data_key, $firstLine)) {
                            $data_key = $instance->getExportTranslationLabel($data_key, $name);

                            if (!\in_array($data_key, $firstLine)) {
                                continue;
                            }
                        }

                        $headers[$key] = $data_key;

                        $headersRevert[$data_key] = $key;

                        $methods[$name] = [
                            'set' => $this->getSetMethod($name, 'set'),
                            'get' => $this->getSetMethod($name, 'get'),
                            'is' => $this->getSetMethod($name, 'is'),
                        ];
                    }

                    continue;
                }

                $data = [];

                foreach ($firstLine as $columnKey => $columnName) {
                    $data[$columnName] = $dataRaw[$columnKey];
                }

                $log = new ImportLog();

                $log->setLine($line);

                //$log->setUploadFile($uploadFile);

                $errors = [];

                $changes = [];

                $oldValueRawArray = [];
                $newValueRawArray = [];

                $identifier_data_key = $headers[$identifier];

                $identifier_value = $data[$identifier_data_key];

                if ($identifier_value) {
                    $entity = $instance->getObject($identifier_value);

                    if (!$entity) {
                        $errors[] = 'Not found';
                    }
                } else {
                    $entity = new $entityClass();
                }

                $valueRawArray = [];

                foreach ($exportFields as $key => $name) {
                    if (!\array_key_exists($key, $headers)) {
                        continue;
                    }

                    $data_key = $headers[$key];

                    $valueRaw = $data[$data_key];

                    $newValueRawArray[$name] = $valueRaw;

                    $value = $valueRaw;

                    if ($name === $identifier) {
                        continue;
                    }

                    /*
                     * Многие делают ошибки в стандартной кодировке,
                     * поэтому на всякий случай провверяем оба варианта написания
                     */
                    if ('utf8' !== $encode && 'utf-8' !== $encode) {
                        $value = iconv($encode, 'utf8//TRANSLIT', $value);
                    }

                    try {
                        $getMethod = $methods[$name]['get'];
                        $setMethod = $methods[$name]['set'];

                        if (!method_exists($entity, $getMethod)) {
                            $getMethod = $methods[$name]['is'];
                        }

                        if (!method_exists($entity, $getMethod)) {
                            continue;
                        }

                        $oldValue = $entity->$getMethod();

                        if (isset($exportFieldsAsString[$name])) {
                            $getMethodRaw = $getMethod . 'ExportAsString';
                            $oldValueRawArray[$name] = $entity->$getMethodRaw();
                        }

                        try {
                            $valueNew = null;

                            $type = '';

                            if (isset($fieldsMappings[$name])) {
                                $type = $fieldsMappings[$name]['type'];
                            }

                            if (isset($associationMappings[$name])) {
                                $associationMappingsItem = $associationMappings[$name];

                                if (ClassMetadataInfo::MANY_TO_MANY === $associationMappingsItem['type']) {
                                    $valueArray = explode(',', $value);

                                    foreach ($valueArray as $valueArrayKey => $valueArrayItem) {
                                        $valueArray[$valueArrayKey] = trim($valueArrayItem);
                                    }

                                    $type = 'many_to_many';

                                    $repo = $em->getRepository($associationMappingsItem['targetEntity']);

                                    $valueCollection = [];

                                    foreach ($valueArray as $valueItem) {
                                        $valueItemObject = $repo->findOneBy([
                                            'name' => $valueItem,
                                        ]);

                                        if ($valueItemObject) {
                                            $valueCollection[$valueItemObject->getId()] = $valueItemObject;
                                        }
                                    }

                                    $valueNew = $valueCollection;
                                }

                                if (ClassMetadataInfo::MANY_TO_ONE === $associationMappingsItem['type']) {
                                    $type = 'entity';

                                    if ('' === $value || null === $value) {
                                        $valueNew = null;
                                    } else {
                                        $repo = $em->getRepository($associationMappingsItem['targetEntity']);

                                        $qb = $repo->createQueryBuilder('entity');

                                        /* @var QueryBuilder $qb */
                                        if (method_exists($instance, 'importFieldHandler')) {
                                            $qb = $instance->importFieldHandler($qb, $entity, $name, $value);
                                        }

                                        $qb->getQuery()->setCacheable(true);
                                        $qb->getQuery()->setResultCacheLifetime(120);
                                        $qb->getQuery()->setQueryCacheLifetime(120);
                                        $result = $qb->getQuery()->getResult();

                                        if (1 === \count($result)) {
                                            $valueNew = $result[0];
                                        } else {
                                            $valueNew = null;

                                            throw new InvalidArgumentException(
                                                sprintf(
                                                    'Edit failed, object with id "%s" not found in association "%s".',
                                                    $value,
                                                    $name)
                                            );
                                        }
                                    }
                                }
                            }

                            if ('date' === $type || 'datetime' === $type) {
                                if ($value) {
                                    $valueNew = new \DateTime($value);

                                    $valueNewTimestamp = $valueNew->getTimestamp();

                                    /** @var \DateTime $oldValueDateTime */
                                    $oldValueDateTime = $oldValue;

                                    $oldValueTimestamp = $oldValueDateTime->getTimestamp();

                                    if ($valueNewTimestamp === $oldValueTimestamp) {
                                        $valueNew = $oldValue;
                                    }
                                } else {
                                    $valueNew = null;
                                }
                            }

                            if ('boolean' === $type) {
                                $valueNew = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                            }

                            if ('integer' === $type) {
                                $valueNew = (int) $value;
                            }
                            if ('text' === $type) {
                                $valueNew = $value;
                            }
                            if ('string' === $type) {
                                $valueNew = $value;
                            }

                            if ('decimal' === $type) {
                                $valueNew = number_format($value, 2, '.', '');
                            }
                        } catch (\Exception $e) {
                            throw new InvalidArgumentException('Field name not found: ' . $e->getMessage());
                        }

                        $value = $valueNew;

                        if (\is_string($value) || null === $value) {
                            if ('setSlug' === $setMethod && '' === $value) {
                                $value = null;
                                $entity->$setMethod($value);
                            }

                            if (null === $oldValue) {
                                $oldValue = '';
                            }

                            if (null === $value) {
                                $value = '';
                            }

                            if ($value !== $oldValue) {
                                $entity->$setMethod($value);
                            }
                        } elseif (\is_bool($oldValue)) {
                            if ($value !== $oldValue) {
                                $entity->$setMethod($value);
                            }
                        } else {
                            $entity->$setMethod($value);
                        }

                        if (isset($exportFieldsAsString[$name])) {
                            $getMethodRaw = $getMethod . 'ExportAsString';
                            $valueRawArray[$name] = $entity->$getMethodRaw();
                        }
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                        break;
                    }
                }

                $idMethod = $methods[$identifier]['get'];

                if ($entity->$idMethod()) {
                    $log->setForeignId($entity->$idMethod());
                }

                if (!\count($errors)) {
                    $errors = $validator->validate($entity);
                }

                if (!\count($errors)) {
                    /*
                     * Если у сещности нет ID, то она новая - добавляем ее
                     */
                    if (!$entity->$idMethod()) {
                        $log->setStatus(ImportLog::STATUS_SUCCESS);
                    } else {
                        $log->setStatus(ImportLog::STATUS_EXISTS);
                    }

                    $uow = $em->getUnitOfWork();

                    $uow->computeChangeSets();

                    $aChangeSet = $uow->getEntityChangeSet($entity);

                    $getScheduledCollectionUpdates = $uow->getScheduledCollectionUpdates();

                    $getScheduledCollectionDeletions = $uow->getScheduledCollectionDeletions();

                    if (!(\count($aChangeSet) || \count($getScheduledCollectionUpdates) || \count($getScheduledCollectionDeletions))) {
                        if (!$entity->$idMethod()) {
                            $log->setStatus(ImportLog::STATUS_SUCCESS);

                            foreach ($newValueRawArray as $newValueRawArrayKey => $newValueRawArrayValue) {
                                $changes[$newValueRawArrayKey] = [
                                    'old' => '',
                                    'new' => $newValueRawArrayValue,
                                ];
                            }

                            $log->setChanges($changes);

                            if (!$isDryRun) {
                                $em->persist($entity);
                                //$this->em->flush($entity);
                                $log->setForeignId($entity->$idMethod());
                            } //else {
                            //$em->detach($entity);
                            //}
                        } else {
                            //$em->detach($entity);

                            $log->setStatus(ImportLog::STATUS_NOCHANGE);
                        }
                    } //else {
                    //if (!$isDryRun) {
                    //$em->persist($entity);
                    //} else {
                    //$em->detach($entity);
                    //}
                    //}

                    foreach ($aChangeSet as $aChangeSetKey => $aChangeSetValue) {
                        if ($aChangeSetValue[0] instanceof \DateTimeInterface && $aChangeSetValue[1] instanceof \DateTimeInterface) {
                            [$old, $new] = $aChangeSetValue;

                            /* @var \DateTime $old */
                            /* @var \DateTime $new */

                            $changes[$aChangeSetKey] = [
                                'old' => (string) $old->format('d.m.Y H:i:s'),
                                'new' => (string) $new->format('d.m.Y H:i:s'),
                            ];
                        } else {
                            $changes[$aChangeSetKey] = [
                                'old' => (string) $aChangeSetValue[0],
                                'new' => (string) $aChangeSetValue[1],
                            ];
                        }
                    }

                    /** @var \Doctrine\ORM\PersistentCollection $getScheduledCollectionUpdates */
                    foreach ($getScheduledCollectionUpdates as $getScheduledCollectionUpdatesKey => $getScheduledCollectionUpdatesValue) {
                        $getScheduledCollectionUpdatesMapping = $getScheduledCollectionUpdatesValue->getMapping();

                        $changes[$getScheduledCollectionUpdatesMapping['fieldName']] = [
                            'old' => $oldValueRawArray[$getScheduledCollectionUpdatesMapping['fieldName']],
                            'new' => $valueRawArray[$getScheduledCollectionUpdatesMapping['fieldName']],
                        ];
                    }

                    $log->setChanges($changes);
                } else {
                    $log->setMessage(json_encode($errors));
                    $log->setStatus(ImportLog::STATUS_ERROR);
                }

                $logs[] = $log;

                //dump($changes);

                //$em->persist($log);

                //$em->flush($log);

                //$em->detach($log);

                if (!$isDryRun) {
                    if (method_exists($entity, 'setUpdatedBy')) {
                        $uploadFile = $em->getRepository(UploadFile::class)->find($uploadFileId);
                        $container->get('stof_doctrine_extensions.listener.blameable')
                            ->setUserValue($uploadFile->getCreatedBy());
                    }

                    $em->persist($entity);
                    $em->flush($entity);
                }

                $em->detach($entity);

                //$em->clear(get_class($entity));
                //$em->clear(get_class($log));

                $em->clear();

                if (0 === ($line % $batchSize)) {
                    $uploadFile = $em->getRepository(UploadFile::class)->find($uploadFileId);

                    /** @var ImportLog $logItem */
                    foreach ($logs as $logItem) {
                        $logItem->setUploadFile($uploadFile);
                        $em->persist($logItem);
                    }

                    $logs = [];

                    $em->flush();
                    $em->clear(); // Detaches all objects from Doctrine!
                }
            }

            $em->flush();

            $em->clear();

            $uploadFile = $em->getRepository(UploadFile::class)->find($uploadFileId);
            $container->get('stof_doctrine_extensions.listener.blameable')
                ->setUserValue($uploadFile->getCreatedBy());

            $em->refresh($uploadFile);

            $uploadFile->setStatus(UploadFile::STATUS_SUCCESS);

            /** @var ImportLog $logItem */
            foreach ($logs as $logItem) {
                $logItem->setUploadFile($uploadFile);
                $em->persist($logItem);
            }

            //$em->flush($uploadFile);

            //$uow = $em->getUnitOfWork();

            $em->flush();

            $em->clear();
        } catch (\Exception $e) {
            throw $e;
            /*
             * Данный хак нужен в случае бросания ORMException
             * В случае бросания ORMException entity manager останавливается
             * и его требуется перезагрузить
             */

            /*
            if (!$em->isOpen()) {
                    $newEm = $em->create(
                        $em->getConnection(),
                        $em->getConfiguration()
                    );

                $uploadFile = $em->getRepository(UploadFile::class)->find($uploadFileId);
            }

            $uploadFile->setStatus(UploadFile::STATUS_ERROR);
            $uploadFile->setMessage($e->getMessage());
            $em->flush($uploadFile);
             */
        }
    }

    /**
     * @param        $name
     * @param string $method
     *
     * @return string
     */
    protected function getSetMethod($name, $method = 'set')
    {
        return $method . str_replace(' ', '', ucfirst(implode('', explode('_', $name))));
    }
}
