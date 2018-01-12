<?php

namespace Compo\FaqBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\FaqBundle\Entity\Faq;
use Compo\FaqBundle\Repository\FaqRepository;
use Compo\Sonata\AdminBundle\Repository\ViewsTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritdoc}
 */
class FaqManager extends BaseEntityManager
{
    use ContainerAwareTrait;
    use ViewsTrait;

    /**
     * @param $criteria
     * @param int $page
     *
     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    public function getPager($criteria, $page = 1)
    {
        $container = $this->getContainer();

        $paginator = $container->get('knp_paginator');

        $settingsManager = $container->get('sylius.settings_manager');

        $settings = $settingsManager->load('compo_faq');

        $limit = $settings->get('faq_per_page');

        $parameters = [];

        /** @var FaqRepository $repository */
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
     *
     * @return array
     */
    public function findLastPublications($limit = 5)
    {
        /** @var FaqRepository $repository */
        $repository = $this->getRepository();

        return $repository->findLastPublications($limit);
    }

    /**
     * @param $slug
     *
     * @return Faq
     */
    public function findBySlug($slug)
    {
        /** @var FaqRepository $repository */
        $repository = $this->getRepository();

        return $repository->findBySlug($slug);
    }

    /**
     * @param Faq $faq
     * @param int $absolute
     *
     * @return string
     */
    public function getArticleShowPermalink(Faq $faq, $absolute = 1)
    {
        return $this->getContainer()->get('router')->generate($this->getArticleShowRoute(), $this->getArticleShowRouteParameters($faq), $absolute);
    }

    /**
     * @return string
     */
    public function getArticleShowRoute()
    {
        return 'compo_faq_show_by_slug';
    }

    /**
     * @param Faq $faq
     *
     * @return array
     */
    public function getArticleShowRouteParameters(Faq $faq)
    {
        return ['slug' => $faq->getSlug()];
    }

    /**
     * @param array $parameters
     * @param int   $absolute
     *
     * @return string
     */
    public function getFaqIndexPermalink(array $parameters = [], $absolute = 1)
    {
        return $this->getContainer()->get('router')->generate($this->getFaqIndexRoute(), $this->getFaqIndexRouteParameters($parameters), $absolute);
    }

    /**
     * @return string
     */
    public function getFaqIndexRoute()
    {
        return 'compo_faq_index';
    }

    /**
     * @param $parameters
     *
     * @return mixed
     */
    public function getFaqIndexRouteParameters($parameters)
    {
        return $parameters;
    }
}
