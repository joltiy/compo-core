<?php

namespace Compo\Sonata\AdminBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Gedmo\SoftDeleteable\SoftDeleteableListener;

class SoftDeleteableTrashFilter extends SQLFilter
{
    protected $listener;
    protected $entityManager;
    protected $disabled = [];
    protected $enabled = [];

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $class = $targetEntity->getName();

        if (count($this->enabled)) {
            if ((array_key_exists($class, $this->enabled) && true === $this->enabled[$class])) {
            } else {
                return '';
            }
        }
        if (array_key_exists($class, $this->disabled) && true === $this->disabled[$class]) {
            return '';
        } elseif (array_key_exists($targetEntity->rootEntityName, $this->disabled) && true === $this->disabled[$targetEntity->rootEntityName]) {
            return '';
        }

        $config = $this->getListener()->getConfiguration($this->getEntityManager(), $targetEntity->name);

        if (!isset($config['softDeleteable']) || !$config['softDeleteable']) {
            return '';
        }

        $conn = $this->getEntityManager()->getConnection();
        $platform = $conn->getDatabasePlatform();
        $quoteStrategy = $this->getEntityManager()->getConfiguration()->getQuoteStrategy();

        $column = $quoteStrategy->getColumnName($config['fieldName'], $targetEntity, $platform);

        $addCondSql = $platform->getIsNotNullExpression($targetTableAlias . '.' . $column);
        if (isset($config['timeAware']) && $config['timeAware']) {
            $now = $conn->quote(date('Y-m-d H:i:s')); // should use UTC in database and PHP
            $addCondSql = "({$addCondSql} OR {$targetTableAlias}.{$column} > {$now})";
        }

        return $addCondSql;
    }

    public function disableForEntity($class)
    {
        $this->disabled[$class] = true;
    }

    public function enableForEntity($class)
    {
        $this->enabled[$class] = true;

        $this->disabled[$class] = false;
    }

    protected function getListener()
    {
        if (null === $this->listener) {
            $em = $this->getEntityManager();
            $evm = $em->getEventManager();

            foreach ($evm->getListeners() as $listeners) {
                foreach ($listeners as $listener) {
                    if ($listener instanceof SoftDeleteableListener) {
                        $this->listener = $listener;

                        break 2;
                    }
                }
            }

            if (null === $this->listener) {
                throw new \RuntimeException('Listener "SoftDeleteableListener" was not added to the EventManager!');
            }
        }

        return $this->listener;
    }

    protected function getEntityManager()
    {
        if (null === $this->entityManager) {
            $refl = new \ReflectionProperty('Doctrine\ORM\Query\Filter\SQLFilter', 'em');
            $refl->setAccessible(true);
            $this->entityManager = $refl->getValue($this);
        }

        return $this->entityManager;
    }
}
