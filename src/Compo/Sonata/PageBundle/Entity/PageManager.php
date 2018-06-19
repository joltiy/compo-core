<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\Entity;

use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SiteInterface;

/**
 * This class manages PageInterface persistency with the Doctrine ORM.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class PageManager extends \Sonata\PageBundle\Entity\PageManager
{
    /**
     * {@inheritdoc}
     */
    public function loadPages(SiteInterface $site, $route_name = 'page_slug', $excludeIds = [])
    {
        $qb = $this->getEntityManager()->getRepository(Page::class)->createQueryBuilder('p', 'p.id');
        $qb->select('p');
        $qb->where('p.site = :site');
        $qb->setParameter('site', $site);
        $qb->orderBy('p.position', 'asc');

        if (null !== $route_name) {
            $qb->andWhere('p.routeName = :routeName');
            $qb->setParameter('routeName', $route_name);
        }

        $query = $qb->getQuery();

        /** @var array $pages */
        $pages = $query->execute();

        /** @var Page $page */
        foreach ($pages as $page) {
            if (\in_array($page->getId(), $excludeIds)) {
                continue;
            }

            $parent = $page->getParent();

            $page->disableChildrenLazyLoading();
            if (!$parent) {
                continue;
            }

            /** @var Page $parent_page */
            $parent_page = $pages[$parent->getId()];
            $parent_page->disableChildrenLazyLoading();
            $parent_page->addChildren($page);
        }

        return $pages;
    }

    /**
     * @param PageInterface $page
     *
     * @return mixed|void
     */
    public function fixUrl(PageInterface $page)
    {
        if ($page->isInternal()) {
            $page->setUrl(null); // internal routes do not have any url ...

            return;
        }

        // hybrid page cannot be altered
        if (!$page->isHybrid()) {
            // make sure Page has a valid url
            if ($page->getParent()) {
                if (!$page->getSlug()) {
                    $page->setSlug(Page::slugify($page->getName()));
                }

                if ('/' === $page->getParent()->getUrl()) {
                    $base = '/';
                } elseif ('/' !== mb_substr($page->getParent()->getUrl(), -1)) {
                    $base = $page->getParent()->getUrl() . '/';
                } else {
                    $base = $page->getParent()->getUrl();
                }

                if ('page_slug' === $page->getRouteName()) {
                    $page->setUrl($base . $page->getSlug() . '/');
                }
            } else {
                // a parent page does not have any slug - can have a custom url ...
                $page->setSlug(null);
                $page->setUrl('/' . $page->getSlug());
            }
        }

        foreach ($page->getChildren() as $child) {
            $this->fixUrl($child);
        }
    }
}
