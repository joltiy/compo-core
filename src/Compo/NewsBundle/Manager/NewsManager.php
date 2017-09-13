<?php

namespace Compo\NewsBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\NewsBundle\Entity\News;
use Compo\NewsBundle\Repository\NewsRepository;
use Compo\Sonata\AdminBundle\Entity\ViewsRepositoryTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritDoc}
 */
class NewsManager extends BaseEntityManager
{
    use ContainerAwareTrait;
    use ViewsRepositoryTrait;

    /**
     * @param $criteria
     * @param int $page
     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    public function getPager($criteria, $page = 1)
    {
        $container = $this->getContainer();

        $paginator = $container->get('knp_paginator');

        $settingsManager = $container->get('sylius.settings_manager');

        $settings = $settingsManager->load('compo_news');

        $limit = $settings->get('news_per_page');

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

        $pagination = $paginator->paginate(
            $qb,
            $page,
            $limit
        );

        return $pagination;
    }

    /**
     * @param int $limit
     * @return array
     */
    public function findLastPublications($limit = 5)
    {
        /** @var NewsRepository $repository */
        $repository = $this->getRepository();

        return $repository->findLastPublications($limit);
    }

    /**
     * @param $slug
     * @return News
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlug($slug)
    {
        /** @var NewsRepository $repository */
        $repository = $this->getRepository();

        return $repository->findBySlug($slug);
    }

    /**
     * @param News $articles
     * @param int $absolute
     * @return string
     */
    public function getNewsShowPermalink(News $articles, $absolute = 1)
    {
        return $this->getContainer()->get('router')->generate($this->getNewsShowRoute(), $this->getNewsShowRouteParameters($articles), $absolute);
    }

    /**
     * @return string
     */
    public function getNewsShowRoute()
    {
        return 'compo_news_show_by_slug';
    }

    /**
     * @param News $articles
     * @return array
     */
    public function getNewsShowRouteParameters(News $articles)
    {
        return array('slug' => $articles->getSlug());
    }

    /**
     * @param array $parameters
     * @param int $absolute
     * @return string
     */
    public function getNewsIndexPermalink(array $parameters = array(), $absolute = 1)
    {
        return $this->getContainer()->get('router')->generate($this->getNewsIndexRoute(), $this->getNewsIndexRouteParameters($parameters), $absolute);
    }

    /**
     * @return string
     */
    public function getNewsIndexRoute()
    {
        return 'compo_news_index';
    }

    /**
     * @param $parameters
     * @return mixed
     */
    public function getNewsIndexRouteParameters($parameters)
    {
        return $parameters;
    }
}