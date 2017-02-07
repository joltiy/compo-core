<?php

namespace Compo\NewsBundle\Manager;

use Compo\NewsBundle\Repository\NewsRepository;
use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;


class NewsManager extends BaseEntityManager
{
    use ContainerAwareTrait;

    public function getPager($criteria, $page = 1) {
        $container = $this->getContainer();

        $compo_news_settings = $container->get('sylius.settings.manager')->load('compo_news_settings');

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
            $qb->andWhere('p.enabled = :enabled');
            $parameters['enabled'] = $criteria['enabled'];
        }

        $qb->setParameters($parameters);



        $paginator  = $this->getContainer()->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $page,
            $limit
        );

        return $pagination;
    }
}