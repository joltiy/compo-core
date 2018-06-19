<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\NewsBundle\Repository;

use Compo\NewsBundle\Entity\News;

/**
 * NewsRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NewsRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllPublicated()
    {
        $qb = $this->createQueryBuilder('p');

        $currentTime = new \DateTime();

        $qb->select('p')
            ->where(
                $qb->expr()->lt('p.publicationAt', ':datetime')
            )
            ->andWhere('p.enabled = 1')
            ->orderBy('p.publicationAt', 'DESC')
            ->setMaxResults(1);

        $qb->setParameter(':datetime', $currentTime);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $slug
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return News
     */
    public function findBySlug($slug)
    {
        $qb = $this->createQueryBuilder('p');

        $currentTime = new \DateTime();

        $qb->select('p')
            ->where(
                $qb->expr()->lt('p.publicationAt', ':datetime')
            )
            ->andWhere('p.enabled = 1')
            ->andWhere('p.slug = :slug')
            ->orderBy('p.publicationAt', 'DESC')
            ->setMaxResults(1);

        $qb->setParameter(':datetime', $currentTime);
        $qb->setParameter(':slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Последнии статьи.
     *
     * @param int $limit
     *
     * @return array
     */
    public function findLastPublications($limit = 5)
    {
        $qb = $this->createQueryBuilder('p');

        $currentTime = new \DateTime();

        $qb->select('p')
            ->where(
                $qb->expr()->lt('p.publicationAt', ':datetime')
            )
            ->andWhere('p.enabled = 1')
            ->orderBy('p.publicationAt', 'DESC')
            ->setMaxResults($limit);

        $qb->setParameter(':datetime', $currentTime);

        return $qb->getQuery()->getResult();
    }
}
