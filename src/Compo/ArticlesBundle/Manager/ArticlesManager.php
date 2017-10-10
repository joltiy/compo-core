<?php

namespace Compo\ArticlesBundle\Manager;

use Compo\ArticlesBundle\Entity\Articles;
use Compo\ArticlesBundle\Repository\ArticlesRepository;
use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Repository\ViewsTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritDoc}
 */
class ArticlesManager extends BaseEntityManager
{
    use ContainerAwareTrait;
    use ViewsTrait;

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

        $settings = $settingsManager->load('compo_articles');

        $limit = $settings->get('articles_per_page');

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
        /** @var ArticlesRepository $repository */
        $repository = $this->getRepository();

        return $repository->findLastPublications($limit);
    }

    /**
     * @param $slug
     * @return Articles
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlug($slug)
    {
        /** @var ArticlesRepository $repository */
        $repository = $this->getRepository();

        return $repository->findBySlug($slug);
    }

    /**
     * @param Articles $articles
     * @param int $absolute
     * @return string
     */
    public function getArticleShowPermalink(Articles $articles, $absolute = 1)
    {
        return $this->getContainer()->get('router')->generate($this->getArticleShowRoute(), $this->getArticleShowRouteParameters($articles), $absolute);
    }

    /**
     * @return string
     */
    public function getArticleShowRoute()
    {
        return 'compo_articles_show_by_slug';
    }

    /**
     * @param Articles $articles
     * @return array
     */
    public function getArticleShowRouteParameters(Articles $articles)
    {
        return array('slug' => $articles->getSlug());
    }

    /**
     * @param array $parameters
     * @param int $absolute
     * @return string
     */
    public function getArticlesIndexPermalink(array $parameters = array(), $absolute = 1)
    {
        return $this->getContainer()->get('router')->generate($this->getArticlesIndexRoute(), $this->getArticlesIndexRouteParameters($parameters), $absolute);
    }

    /**
     * @return string
     */
    public function getArticlesIndexRoute()
    {
        return 'compo_articles_index';
    }

    /**
     * @param $parameters
     * @return mixed
     */
    public function getArticlesIndexRouteParameters($parameters)
    {
        return $parameters;
    }
}
