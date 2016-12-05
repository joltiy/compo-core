<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\Entity;

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
    public function loadPages(SiteInterface $site, $route_name = 'page_slug')
    {

        if (is_null($route_name)) {
            $query = $this->getEntityManager()
                ->createQuery(sprintf('SELECT p FROM %s p INDEX BY p.id WHERE p.site = %d ORDER BY p.position ASC', $this->class, $site->getId()));
        } else {
            $query = $this->getEntityManager()
                ->createQuery(sprintf('SELECT p FROM %s p INDEX BY p.id WHERE p.site = %d AND p.routeName = \'%s\' ORDER BY p.position ASC', $this->class, $site->getId(), $route_name));
        }


        $pages = $query->execute();

        foreach ($pages as $page) {
            $parent = $page->getParent();

            $page->disableChildrenLazyLoading();
            if (!$parent) {
                continue;
            }

            $pages[$parent->getId()]->disableChildrenLazyLoading();
            $pages[$parent->getId()]->addChildren($page);
        }

        return $pages;
    }
}
