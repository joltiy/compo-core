<?php

namespace Compo\ArticlesBundle\Repository;

/**
 * ArticlesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticlesRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $slug
     * @return null
     */
    public function findBySlug($slug) {
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

        $result = $qb->getQuery()->getResult();

        if ($result) {
            return $result[0];
        } else {
            return null;
        }
    }

    /**
     * Последнии статьи
     *
     * @param int $limit
     * @return array
     */
    public function findLastPublications($limit = 5) {
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
