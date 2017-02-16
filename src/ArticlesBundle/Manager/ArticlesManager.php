<?php

namespace Compo\ArticlesBundle\Manager;

use Compo\ArticlesBundle\Repository\ArticlesRepository;
use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritDoc}
 */
class ArticlesManager extends BaseEntityManager
{
    use ContainerAwareTrait;

    public function getPager($criteria, $page = 1)
    {
        $container = $this->getContainer();

        $compo_articles_settings = $container->get('sylius.settings.manager')->load('compo_articles');

        $limit = $compo_articles_settings->get('articles_per_page');

        $parameters = array();

        /** @var ArticlesRepository $repository */
        $repository = $this->getRepository();

        $qb = $repository->createQueryBuilder('p');

        $qb->select('p')->orderBy('p.publicationAt', 'DESC');

        if (!isset($criteria['enabled'])) {
            $criteria['enabled'] = true;
        }

        if (isset($criteria['enabled'])) {
            $currentTime = new \DateTime();

            $qb->andWhere('p.enabled = :enabled');
            $qb->andWhere(
                $qb->expr()->lt('p.publicationAt', ':datetime')
            );

            $parameters['datetime'] = $currentTime;
            $parameters['enabled'] = $criteria['enabled'];
        }

        $qb->setParameters($parameters);

        $paginator = $this->getContainer()->get('knp_paginator');

        $pagination = $paginator->paginate(
            $qb,
            $page,
            $limit
        );
        return $pagination;
    }
}