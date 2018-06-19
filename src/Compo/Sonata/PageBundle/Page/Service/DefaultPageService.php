<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\Page\Service;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\SeoBundle\Seo\SeoPage;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SnapshotPageProxy;

/**
 * Default page service to render a page template.
 *
 * Note: this service is backward-compatible and functions like the old page renderer class.
 *
 * @author Olivier Paradis <paradis.olivier@gmail.com>
 */
class DefaultPageService extends \Sonata\PageBundle\Page\Service\DefaultPageService
{
    use ContainerAwareTrait;

    /**
     * Updates the SEO page values for given page instance.
     *
     * @param PageInterface $pageIterface
     *
     * @throws \Throwable
     */
    protected function updateSeoPage(PageInterface $pageIterface)
    {
        /** @var SeoPage $seoPage */
        $seoPage = $this->seoPage;

        if ($pageIterface instanceof SnapshotPageProxy) {
            $page = $pageIterface->getPage();
        } else {
            $page = $pageIterface;
        }

        $seoPage->addVar('page_internal', $page);

        $route = $page->getRouteName();

        if ('page_slug' === $route) {
            $seoPage->setLinkCanonical($this->getContainer()->get('router')->generate(
                $route,
                [
                    'path' => $page->getUrl(),
                ],
                0
            ));
        }

        if ('page_slug' === $route
            ||
            '_page_internal_error_not_found' === $route
        ) {
            $seoPage->build();
        }
    }
}
