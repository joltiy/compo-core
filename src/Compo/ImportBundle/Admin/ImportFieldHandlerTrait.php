<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ImportBundle\Admin;

use Doctrine\ORM\QueryBuilder;

/**
 * Trait ImportFieldHandlerTrait.
 */
trait ImportFieldHandlerTrait
{
    /**
     * @param $qb QueryBuilder
     * @param $subject
     * @param $field
     * @param $value
     *
     * @return QueryBuilder
     */
    public function importFieldHandler($qb, $subject, $field, $value)
    {
        $qb->andWhere('entity.name = :value');
        $qb->setParameter('value', $value);
        $qb->setMaxResults(1);

        return $qb;
    }
}
