<?php

namespace Compo\CoreBundle\Repository;

use Doctrine\DBAL\Query\QueryBuilder;

trait OrderByNameTrait
{
    public function createQueryBuilderOrderByName()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $qb = parent::createQueryBuilder('c');
        /** @var QueryBuilder $qb */
        $qb->select('c');
        $qb->orderBy('c.name', 'ASC');

        return $qb;
    }
}