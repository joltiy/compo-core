<?php

namespace Compo\NewsBundle\Manager;

use Compo\NewsBundle\Repository\NewsRepository;
use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritDoc}
 */
class NewsManager extends BaseEntityManager
{
    use ContainerAwareTrait;

    public function getPager($criteria, $page = 1)
    {
        $container = $this->getContainer();

        $compo_news_settings = $container->get('sylius.settings.manager')->load('compo_news');

        $limit = $compo_news_settings->get('news_per_page');

        $parameters = array();

        /** @var NewsRepository $repository */
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