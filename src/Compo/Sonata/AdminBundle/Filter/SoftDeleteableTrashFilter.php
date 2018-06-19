<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Gedmo\Mapping\MappedEventSubscriber;
use Gedmo\SoftDeleteable\SoftDeleteableListener;

/**
 * Фильтрация в корзине.
 */
class SoftDeleteableTrashFilter extends SQLFilter
{
    /**
     * @var
     */
    protected $listener;
    /**
     * @var
     */
    protected $entityManager;
    /**
     * @var array
     */
    protected $disabled = [];
    /**
     * @var array
     */
    protected $enabled = [];

    /**
     * @param ClassMetadata $targetEntity
     * @param string        $targetTableAlias
     *
     * @throws \ReflectionException
     *
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $class = $targetEntity->getName();

        if (\count($this->enabled) && !(array_key_exists($class, $this->enabled) && true === $this->enabled[$class])) {
            return '';
        }

        if (array_key_exists($class, $this->disabled) && true === $this->disabled[$class]) {
            return '';
        }

        if (array_key_exists($targetEntity->rootEntityName, $this->disabled) && true === $this->disabled[$targetEntity->rootEntityName]) {
            return '';
        }

        $em = $this->getEntityManager();

        $config = $this->getListener()->getConfiguration($em, $targetEntity->name);

        if (!isset($config['softDeleteable']) || !$config['softDeleteable']) {
            return '';
        }

        /** @var \Doctrine\DBAL\Connection $conn */
        $conn = $em->getConnection();

        $platform = $conn->getDatabasePlatform();

        $quoteStrategy = $em->getConfiguration()->getQuoteStrategy();

        $column = $quoteStrategy->getColumnName($config['fieldName'], $targetEntity, $platform);

        $addCondSql = $platform->getIsNotNullExpression($targetTableAlias . '.' . $column);

        if (!empty($config['timeAware'])) {
            $now = $conn->quote(date('Y-m-d H:i:s')); // should use UTC in database and PHP
            $addCondSql = "({$addCondSql} OR {$targetTableAlias}.{$column} > {$now})";
        }

        return $addCondSql;
    }

    /**
     * @throws \ReflectionException
     *
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        if (null === $this->entityManager) {
            $reflectionProperty = new \ReflectionProperty(SQLFilter::class, 'em');
            $reflectionProperty->setAccessible(true);
            $this->entityManager = $reflectionProperty->getValue($this);
        }

        return $this->entityManager;
    }

    /**
     * @throws \ReflectionException
     *
     * @return SoftDeleteableListener
     */
    protected function getListener()
    {
        if (null === $this->listener) {
            $em = $this->getEntityManager();
            $evm = $em->getEventManager();

            foreach ($evm->getListeners() as $listeners) {
                /** @var array $listeners */
                /** @var MappedEventSubscriber $listener */
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

    /**
     * @param $class
     */
    public function disableForEntity($class)
    {
        $this->disabled[$class] = true;
    }

    /**
     * @param $class
     */
    public function enableForEntity($class)
    {
        $this->enabled[$class] = true;

        $this->disabled[$class] = false;
    }
}
