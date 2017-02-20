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
use Sonata\PageBundle\Page\TemplateManagerInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     */
    protected function updateSeoPage(PageInterface $page)
    {
        $this->seoPage->addTemplates('seo_page_internal', array(
            'header' => '{{ page_internal.header }}',
            'title' => '{{ page_internal.title }}',
            'meta_keyword' => '{{ page_internal.metaKeyword }}',
            'meta_description' => '{{ page_internal.metaDescription }}',
            'description' => '{{ page_internal.description }}',
        ));

        $this->seoPage->addVar('page_internal', $page);

        $this->seoPage->build();


    }
}
