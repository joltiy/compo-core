<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\Page\Service;

use Sonata\PageBundle\Model\PageInterface;


/**
 * Default page service to render a page template.
 *
 * Note: this service is backward-compatible and functions like the old page renderer class.
 *
 * @author Olivier Paradis <paradis.olivier@gmail.com>
 */
class DefaultPageService extends \Sonata\PageBundle\Page\Service\DefaultPageService
{
    /**
     * Updates the SEO page values for given page instance.
     *
     * @param PageInterface $page
     * @throws \Throwable
     */
    protected function updateSeoPage(PageInterface $page)
    {
        $this->seoPage->addVar('page_internal', $page);

        $route = $page->getRouteName();

        if ($route === 'page_slug'

            ||
            $route === '_page_internal_error_not_found'

        ) {
            $this->seoPage->build();
        }
    }
}
