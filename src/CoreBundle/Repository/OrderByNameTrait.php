<?php

namespace Compo\CoreBundle\Repository;

trait OrderByNameTrait
{
    public function createQueryBuilderOrderByName()
    {
        $qb = parent::createQueryBuilder('c');
        $qb->select('c');
        $qb->orderBy('c.name', 'ASC');

        return $qb;
    }
}