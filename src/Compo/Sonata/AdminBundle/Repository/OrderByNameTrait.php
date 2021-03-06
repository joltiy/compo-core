<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

/**
 * Trait OrderByNameTrait.
 */
trait OrderByNameTrait
{
    /**
     * @return QueryBuilder
     */
    public function createQueryBuilderOrderByName()
    {
        /** @var EntityRepository $repository */
        $repository = $this;

        $qb = $repository->createQueryBuilder('c');

        /* @var QueryBuilder $qb */
        $qb->select('c');
        $qb->orderBy('c.name', 'ASC');

        return $qb;
    }
}
