<?php

namespace Compo\Sonata\AdminBundle\Repository;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Trait OrderByNameTrait
 * @package Compo\Sonata\AdminBundle\Entity
 */
trait OrderByNameTrait
{
    /**
     * @return QueryBuilder
     */
    public function createQueryBuilderOrderByName()
    {
        $qb = parent::createQueryBuilder('c');
        /** @var QueryBuilder $qb */
        $qb->select('c');
        $qb->orderBy('c.name', 'ASC');

        return $qb;
    }
}
