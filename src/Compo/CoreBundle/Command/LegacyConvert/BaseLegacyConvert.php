<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\CoreBundle\Command\BaseLegacyConvertCommand;
use SimpleThings\EntityAudit\EventListener\LogRevisionsListener;

/**
 * Class BaseLegacyConvert.
 */
class BaseLegacyConvert
{
    /** @var BaseLegacyConvertCommand */
    public $command;

    /**
     * @var
     */
    public $headerField = 'header';

    /**
     * @var
     */
    public $idFields = [
        'id' => 'id',
    ];

    /**
     * @var int
     */
    public $batchSize = 0;

    /**
     * @var
     */
    public $repositoryName;

    /**
     * @var
     */
    public $entityClass;
    /**
     * @var
     */
    public $tableName;

    /**
     * @return mixed
     */
    public function getIdFields()
    {
        return $this->idFields;
    }

    /**
     * @param mixed $idFields
     */
    public function setIdFields($idFields)
    {
        $this->idFields = $idFields;
    }

    public function configure()
    {
    }

    /**
     * @return int
     */
    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    /**
     * @param int $batchSize
     */
    public function setBatchSize(int $batchSize)
    {
        $this->batchSize = $batchSize;
    }

    public function process()
    {
        $searchedListener = null;
        $em = $this->getEntityManager();

        foreach ($em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $key => $listener) {
                if ($listener instanceof LogRevisionsListener) {
                    $searchedListener = $listener;
                    break 2;
                }
            }
        }

        if ($searchedListener) {
            $evm = $em->getEventManager();
            $evm->removeEventListener(['onFlush', 'postPersist', 'postUpdate', 'postFlush'], $searchedListener);
        }

        $this->getCommand()->getIo()->section('Load: ' . $this->getRepositoryName());

        $currentRepository = $this->getCommand()->getCurrentRepository($this->getRepositoryName());

        $this->getCommand()->clearCurrent($currentRepository);

        $oldData = $this->getOldData();

        $this->getCommand()->getIo()->progressStart(count($oldData));

        $batchSize = $this->batchSize;

        $i = 0;

        foreach ($oldData as $oldDataItemKey => $oldDataItem) {
            ++$i;

            $newItem = null;

            if (!$this->getCommand()->isDrop()) {
                $idsFields = [];

                foreach ($oldDataItem as $oldDataItemFieldKey => $oldDataItemFieldValue) {
                    if (isset($this->idFields[$oldDataItemFieldKey])) {
                        $idsFields[$this->idFields[$oldDataItemFieldKey]] = $oldDataItemFieldValue;
                    }
                }

                $newItem = $currentRepository->findOneBy($idsFields);
            }

            $this->getCommand()->getIo()->writeln('');

            if ($newItem) {
                $this->getCommand()->getIo()->note($this->getRepositoryName() . ' (OLD): ' . $oldDataItem[$this->getHeaderField()]);
            } else {
                $this->getCommand()->getIo()->note($this->getRepositoryName() . ' (NEW): ' . $oldDataItem[$this->getHeaderField()]);

                $class = $this->getEntityClass();

                $newItem = new $class();
            }

            $this->getCommand()->changeIdGenerator($newItem);

            $result = $this->iterateItem($oldDataItemKey, $oldDataItem, $newItem);

            if (false !== $result) {
                $this->getCommand()->getEntityManager()->persist($newItem);
            }

            if ($batchSize) {
                if (0 === ($i % $batchSize)) {
                    $this->getCommand()->getEntityManager()->flush();
                    $this->getCommand()->getEntityManager()->clear();
                    gc_collect_cycles();
                }
            } else {
                $this->getCommand()->getEntityManager()->flush();
                $this->getCommand()->getEntityManager()->clear();
                gc_collect_cycles();
            }

            unset($oldData[$oldDataItemKey]);

            $this->getCommand()->getIo()->progressAdvance();
        }

        $this->getCommand()->getEntityManager()->flush();
        $this->getCommand()->getEntityManager()->clear();
        gc_collect_cycles();

        $this->getCommand()->getIo()->progressFinish();

        $this->getCommand()->getIo()->success('Load: ' . $this->getRepositoryName());
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getCommand()->getEntityManager();
    }

    /**
     * @return BaseLegacyConvertCommand
     */
    public function getCommand(): BaseLegacyConvertCommand
    {
        return $this->command;
    }

    /**
     * @param BaseLegacyConvertCommand $command
     */
    public function setCommand(BaseLegacyConvertCommand $command)
    {
        $this->command = $command;
    }

    /**
     * @return mixed
     */
    public function getRepositoryName()
    {
        return $this->repositoryName;
    }

    /**
     * @param mixed $repositoryName
     */
    public function setRepositoryName($repositoryName)
    {
        $this->repositoryName = $repositoryName;
    }

    /**
     * @return array
     */
    public function getOldData()
    {
        return $this->getCommand()->getOldData($this->getTableName());
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param mixed $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return mixed
     */
    public function getHeaderField()
    {
        return $this->headerField;
    }

    /**
     * @param mixed $headerField
     */
    public function setHeaderField($headerField)
    {
        $this->headerField = $headerField;
    }

    /**
     * @return mixed
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param mixed $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem
     *
     * @return bool
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        return false;
    }

    /**
     * @param $id
     *
     * @return \Compo\Sonata\MediaBundle\Entity\Media|null
     */
    public function downloadMedia($id)
    {
        return $this->getCommand()->downloadMedia($id);
    }
}
