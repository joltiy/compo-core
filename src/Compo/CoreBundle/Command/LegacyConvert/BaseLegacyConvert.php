<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\CoreBundle\Command\BaseLegacyConvertCommand;
use Compo\CoreBundle\Command\LegacyConvertDatabaseCommand;


/**
 * Class BaseLegacyConvert
 * @package Compo\CoreBundle\Command\LegacyConvert
 */
class BaseLegacyConvert
{
    /** @var  BaseLegacyConvertCommand */
    public $command;

    /**
     * @var
     */
    public $headerField = 'header';

    /**
     * @var
     */
    public $idFields = array(
        'id' => 'id'
    );

    /**
     * @var
     */
    public $repositoryName;

    /**
     * @var
     */
    public $entityClass;

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
     * @var
     */
    public $tableName;

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
     *
     */
    public function configure() {

    }

    /**
     * @return array
     */
    public function getOldData() {
        return $this->getCommand()->getOldData($this->getTableName());
    }

    /**
     *
     */
    public function process() {
        $this->getCommand()->getIo()->section('Load: ' . $this->getRepositoryName());

        $currentRepository = $this->getCommand()->getCurrentRepository($this->getRepositoryName());

        $this->getCommand()->clearCurrent($currentRepository);

        $oldData = $this->getOldData();

        $this->getCommand()->getIo()->progressStart(count($oldData));


        foreach ($oldData as $oldDataItemKey => $oldDataItem) {
            $newItem = null;

            if (!$this->getCommand()->isDrop()) {
                $idsFields = array();

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

                $newItem = new $class;
            }

            $this->getCommand()->changeIdGenerator($newItem);

            $this->iterateItem($oldDataItemKey, $oldDataItem, $newItem);

            $this->getCommand()->getEntityManager()->persist($newItem);
            $this->getCommand()->getEntityManager()->flush();
            $this->getCommand()->getEntityManager()->clear();

            unset($oldData[$oldDataItemKey]);
            $this->getCommand()->getIo()->progressAdvance();
        }

        $this->getCommand()->getIo()->progressFinish();

        $this->getCommand()->getIo()->success('Load: ' . $this->getRepositoryName());
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem) {

    }

    /**
     * @param $id
     * @return \Compo\Sonata\MediaBundle\Entity\Media|null
     */
    public function downloadMedia($id) {
        return $this->getCommand()->downloadMedia($id);
    }


    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->getCommand()->getEntityManager();
    }
}