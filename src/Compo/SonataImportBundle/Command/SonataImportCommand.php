<?php

namespace Compo\SonataImportBundle\Command;

use Compo\SonataImportBundle\Entity\ImportLog;
use Compo\SonataImportBundle\Entity\UploadFile;
use Compo\SonataImportBundle\Loaders\FileLoaderInterface;
use Compo\SonataImportBundle\Service\ImportInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;

class SonataImportCommand extends ContainerAwareCommand
{
    /** @var EntityManager $this->em */
    protected $em;

    protected function configure()
    {
        $this
            ->setName('compo:sonata:import')
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
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batchSize = 100;
        $i = 1;

        $isDryRun = $input->getOption('dry-run');

        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $uploadFileId = $input->getArgument('csv_file');
        $adminCode = $input->getArgument('admin_code');
        $encode = mb_strtolower($input->getArgument('encode'));
        $fileLoaderId = $input->getArgument('file_loader');

        /** @var UploadFile $uploadFile */
        $uploadFile = $this->em->getRepository('CompoSonataImportBundle:UploadFile')->find($uploadFileId);
        $fileLoaders = $this->getContainer()->getParameter('compo_sonata_import.class_loaders');
        $fileLoader = isset($fileLoaders[$fileLoaderId], $fileLoaders[$fileLoaderId]['class']) ?
            $fileLoaders[$fileLoaderId]['class'] :
            null;

        if (!class_exists($fileLoader)) {
            $uploadFile->setStatus(UploadFile::STATUS_ERROR);
            $uploadFile->setMessage('class_loader not found');
            $this->em->flush($uploadFile);

            return;
        }
        $fileLoader = new $fileLoader();
        if (!$fileLoader instanceof FileLoaderInterface) {
            $uploadFile->setStatus(UploadFile::STATUS_ERROR);
            $uploadFile->setMessage('class_loader must be instanceof "FileLoaderInterface"');
            $this->em->flush($uploadFile);

            return;
        }

        try {
            $fileLoader->setFile(new File($uploadFile->getFile()));

            $pool = $this->getContainer()->get('sonata.admin.pool');
            /** @var AbstractAdmin $instance */
            $instance = $pool->getInstance($adminCode);
            $entityClass = $instance->getClass();
            $meta = $this->em->getClassMetadata($entityClass);
            $identifier = $meta->getSingleIdentifierFieldName();
            $exportFields = $instance->getExportFields();
            //$form = $instance->getFormBuilder();
            $formBuilder = $instance->getFormBuilder();

            $firstLine = [];

            $exportFieldsAsString = [];

            foreach ($exportFields as $exportFieldKey => $exportField) {
                if (false !== mb_strpos($exportField, 'ExportAsString')) {
                    $exportFieldsAsString[$exportFieldKey] = $exportFieldKey;
                    $exportFields[$exportFieldKey] = str_replace('ExportAsString', '', $exportField);
                }
            }

            if (0 === $uploadFile->getLoaderClass()) {
                $iterator = $fileLoader->getRows();
            } else {
                $iterator = $fileLoader->getIteration();
            }

            foreach ($iterator as $line => $dataRaw) {
                if (0 === $line) {
                    $firstLine = $dataRaw;
                    continue;
                }

                $data = [];

                foreach ($firstLine as $columnKey => $columnName) {
                    $data[$columnName] = $dataRaw[$columnKey];
                }

                $log = new ImportLog();
                $log
                    ->setLine($line)
                    ->setUploadFile($uploadFile)
                ;

                $entity = new $entityClass();
                $errors = [];

                $changes = [];
                $oldValueRawArray = [];
                $newValueRawArray = [];

                foreach ($exportFields as $key => $name) {
                    if (!isset($data[$key])) {
                        $transLabel = $instance->getExportTranslationLabel($key, $name);

                        if (array_key_exists($transLabel, $data)) {
                            $data_key = $transLabel;
                        } else {
                            continue;
                        }
                    } else {
                        $data_key = $key;
                    }

                    $valueRaw = $value = isset($data[$data_key]) ? $data[$data_key] : '';

                    $newValueRawArray[$name] = $valueRaw;

                    /*
                     * В случае если указан ID (первый столбец)
                     * ищем сущность в базе
                     */
                    if ($name === $identifier) {
                        if ($value) {
                            $oldEntity = $instance->getObject($value);

                            if ($oldEntity) {
                                $entity = $oldEntity;
                            }
                        }
                        continue;
                    }

                    /**
                     * Поля форм не всегда соответствуют тому, что есть на сайте, и что в админке
                     * Поэтому если поле не указано в админке, то просто пропускаем его.
                     */
                    //if (!$form->has($name)) {

                    //continue;
                    //}



                    /*
                     * Многие делают ошибки в стандартной кодировке,
                     * поэтому на всякий случай провверяем оба варианта написания
                     */
                    if ('utf8' !== $encode && 'utf-8' !== $encode) {
                        $value = iconv($encode, 'utf8//TRANSLIT', $value);
                    }

                    try {
                        $getMethod = $this->getSetMethod($name, 'get');

                        $oldValue = $entity->$getMethod();
                        $oldValueRaw = $oldValue;

                        if (isset($exportFieldsAsString[$name])) {
                            $getMethodRaw = $getMethod . 'ExportAsString';

                            $oldValueRaw = $entity->$getMethodRaw();
                            $oldValueRawArray[$name] = $oldValueRaw;
                        }

                        $field = $formBuilder->get($name);

                        $method = $this->getSetMethod($name);

                        /*
                        if ($method == 'setEnabled') {
                            dump($method);
                            dump($value);
                        }
                        */

                        $value = $this->setValue($entity, $value, $oldValue, $field, $instance);

                        /*
                        if ($method == 'setEnabled') {
                            dump($value);
                            dump($oldValue);
                        }
                        */


                        if (is_string($value) || null === $value) {
                            if ('setSlug' === $method && '' === $value) {
                                $value = null;
                                $entity->$method($value);
                            }


                            if ($value !== $oldValue) {
                                $entity->$method($value);
                            }

                        } elseif (is_bool($oldValue)) {
                            if ($value !== $oldValue) {
                                $entity->$method($value);
                            }
                        } else {
                            $entity->$method($value);
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

                $idMethod = $this->getSetMethod($identifier, 'get');

                if ($entity->$idMethod()) {
                    $log->setForeignId($entity->$idMethod());
                }

                if (!count($errors)) {
                    $validator = $this->getContainer()->get('validator');
                    $errors = $validator->validate($entity);
                }

                if (!count($errors)) {
                    $idMethod = $this->getSetMethod($identifier, 'get');

                    /*
                     * Если у сещности нет ID, то она новая - добавляем ее
                     */
                    if (!$entity->$idMethod()) {
                        $log->setStatus(ImportLog::STATUS_SUCCESS);
                    } else {
                        $log->setStatus(ImportLog::STATUS_EXISTS);
                    }
                    $uow = $this->em->getUnitOfWork();

                    $uow->computeChangeSets();

                    $aChangeSet = $uow->getEntityChangeSet($entity);

                    //dump($aChangeSet);

                    $getScheduledCollectionUpdates = $uow->getScheduledCollectionUpdates();

                    $getScheduledCollectionDeletions = $uow->getScheduledCollectionDeletions();

                    if (count($aChangeSet) || count($getScheduledCollectionUpdates) || count($getScheduledCollectionDeletions)) {
                        if (!$isDryRun) {
                            $this->em->flush($entity);
                        }
                    } else {
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
                                $this->em->persist($entity);
                                $this->em->flush($entity);
                                $log->setForeignId($entity->$idMethod());
                            }
                        } else {
                            $log->setStatus(ImportLog::STATUS_NOCHANGE);
                        }
                    }

                    foreach ($aChangeSet as $aChangeSetKey => $aChangeSetValue) {
                        if ($aChangeSetValue[0] instanceof \DateTimeInterface) {
                            $changes[$aChangeSetKey] = [
                                'old' => (string) $aChangeSetValue[0]->format('d.m.Y H:i:s'),
                                'new' => (string) $aChangeSetValue[1]->format('d.m.Y H:i:s'),
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

                    $uow->detach($entity);

                    $log->setChanges($changes);
                } else {
                    $log->setMessage(json_encode($errors));
                    $log->setStatus(ImportLog::STATUS_ERROR);
                }

                $this->em->persist($log);
                $this->em->flush($log);
            }

            $uploadFile->setStatus(UploadFile::STATUS_SUCCESS);
            $this->em->flush($uploadFile);
        } catch (\Exception $e) {
            throw $e;
            /*
             * Данный хак нужен в случае бросания ORMException
             * В случае бросания ORMException entity manager останавливается
             * и его требуется перезагрузить
             */
            if (!$this->em->isOpen()) {
                $this->em = $this->em->create(
                    $this->em->getConnection(),
                    $this->em->getConfiguration()
                );
                $uploadFile = $this->em->getRepository('CompoSonataImportBundle:UploadFile')->find($uploadFileId);
            }

            $uploadFile->setStatus(UploadFile::STATUS_ERROR);
            $uploadFile->setMessage($e->getMessage());
            $this->em->flush($uploadFile);
        }
    }

    protected function getSetMethod($name, $method = 'set')
    {
        return $method . str_replace(' ', '', ucfirst(implode('', explode('_', $name))));
    }

    protected function setValue($subject, $value, $oldValue, $fieldDescription, AbstractAdmin $admin)
    {
        $mappings = $this->getContainer()->getParameter('compo_sonata_import.mappings');

        $originalValue = $value;

        $field = $fieldDescription->getName();
        $fieldDescriptionType = $fieldDescription->getType();

        if ($fieldDescriptionType->getParent() && 'form' !== $fieldDescriptionType->getParent()->getBlockPrefix()) {
            $type = $fieldDescriptionType->getParent()->getBlockPrefix();
        } else {
            $type = $fieldDescriptionType->getBlockPrefix();
        }

        $metaData = $admin->getModelManager()
            ->getEntityManager($admin->getClass())->getClassMetadata($admin->getClass());

        $associationMappings = $metaData->associationMappings;
        $fieldsMappings = $metaData->fieldMappings;

        if (isset($fieldsMappings[$field])) {
            $type = $fieldsMappings[$field]['type'];
        }

        $valueArray = [];

        if (isset($associationMappings[$field])) {
            $associationMappingsItem = $associationMappings[$field];

            if (ClassMetadataInfo::MANY_TO_MANY === $associationMappingsItem['type']) {
                $valueArray = explode(',', $value);

                foreach ($valueArray as $valueArrayKey => $valueArrayItem) {
                    $valueArray[$valueArrayKey] = trim($valueArrayItem);
                }

                $type = 'many_to_many';
            }

            if (ClassMetadataInfo::MANY_TO_ONE === $associationMappingsItem['type']) {
                $type = 'entity';
            }
        }

        /*
         * Проверяем кастомные типы форм на наличие в конфиге.
         * В случае совпадения, получаем значение из класса, указанного в конфиге
         */
        foreach ($mappings as $item) {
            if ($item['name'] === $type) {
                if ($this->getContainer()->has($item['class']) && $this->getContainer()->get($item['class']) instanceof ImportInterface) {
                    /** @var ImportInterface $class */
                    $class = $this->getContainer()->get($item['class']);

                    return $class->getFormatValue($value);
                }
            }
        }

        if ('date' === $type || 'datetime' === $type) {
            if ($value) {
                $value = new \DateTime($value);
                if ($value === $oldValue) {
                    $value = $oldValue;
                }
            } else {
                $value = null;
            }
        }
        if ('boolean' === $type) {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        if ('integer' === $type) {
            $value = (int) $value;
        }

        if ('decimal' === $type) {
            $value = number_format($value, 2, '.', '');
        }
        if ('many_to_many' === $type) {
            $repo = $admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager()
                ->getRepository($fieldDescription->getOption('class'));

            $valueCollection = [];

            foreach ($valueArray as $valueItem) {
                $valueItemObject = $repo->findOneBy([
                    'name' => $valueItem,
                ]);

                if ($valueItemObject) {
                    $valueCollection[$valueItemObject->getId()] = $valueItemObject;
                }
            }

            return $valueCollection;
        }

        if (
            (
                'choice' === $type &&
                $fieldDescription->getOption('class')
            ) || (
                'entity' === $type &&
                $fieldDescription->getOption('class')
            )
        ) {
            if (!$value) {
                return null;
            }
            /** @var \Doctrine\ORM\Mapping\ClassMetadata $metaData */
            $metaData = $admin->getModelManager()
                ->getEntityManager($admin->getClass())->getClassMetadata($admin->getClass());
            $associations = $metaData->getAssociationNames();

            if (!in_array($field, $associations, true)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Unknown association "%s", association does not exist in entity "%s", available associations are "%s".',
                        $field,
                        $admin->getClass(),
                        implode(', ', $associations)
                    )
                );
            }

            /** @var EntityRepository $repo */
            $repo = $admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager()
                ->getRepository($fieldDescription->getOption('class'));

            /*
             * Если значение число, то пытаемся найти его по ID.
             * Если значение не число, то ищем его по полю name
             */
            if (false && is_numeric($value)) {
                $value = $repo->find($value);
            } else {
                try {
                    $qb = $repo->createQueryBuilder('entity');

                    /** @var QueryBuilder $qb */
                    $qb = $admin->importFieldHandler($qb, $subject, $field, $value);
                    $qb->getQuery()->setCacheable(true);
                    $qb->getQuery()->setResultCacheLifetime(120);
                    $qb->getQuery()->setQueryCacheLifetime(120);
                    $result = $qb->getQuery()->getResult();

                    if (1 === count($result)) {
                        $value = $result[0];
                    } else {
                        $value = null;
                    }
                } catch (ORMException $e) {
                    throw new InvalidArgumentException('Field name not found');
                }
            }

            if (!$value) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Edit failed, object with id "%s" not found in association "%s".',
                        $originalValue,
                        $field)
                );
            }
        }

        return $value;
    }
}
