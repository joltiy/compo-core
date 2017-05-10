<?php

namespace Compo\NewsBundle\Seo;

use Compo\NewsBundle\Entity\News;
use Compo\SeoBundle\Service\BaseService;

/**
 * {@inheritDoc}
 */
class NewsSeoService extends BaseService
{
    /**
     * {@inheritDoc}
     */
    public function build()
    {
        $container = $this->getContainer();

        $seoPage = $this->getSeoPage();

        $articlesManager = $container->get('compo_news.manager.news');
        $settingsManager = $container->get('sylius.settings.manager');

        $articlesSettings = $settingsManager->load('compo_news');

        /** @var News $article */
        $article = $seoPage->getVar('news');

        if ($article) {
            $seoPage->addTemplates('seo_items', array(
                'header' => $articlesSettings->get('seo_items_header'),
                'title' => $articlesSettings->get('seo_items_title'),
                'meta_keyword' => $articlesSettings->get('seo_items_meta_keyword'),
                'meta_description' => $articlesSettings->get('seo_items_meta_description'),
            ));

            $seoPage->addTemplates('news', array(
                'header' => $article->getHeader(),
                'title' => $article->getTitle(),
                'meta_keyword' => $article->getMetaKeyword(),
                'meta_description' => $article->getMetaDescription(),
            ));

            $seoPage->setLinkCanonical($articlesManager->getNewsShowPermalink($article));
        } else {
            $seoPage->addTemplates('seo_index', array(
                'header' => $articlesSettings->get('seo_index_header'),
                'description' => $articlesSettings->get('seo_index_description'),
                'title' => $articlesSettings->get('seo_index_title'),
                'meta_keyword' => $articlesSettings->get('seo_index_meta_keyword'),
                'meta_description' => $articlesSettings->get('seo_index_meta_description'),
            ));

            $page = $seoPage->getVar('page');
            $totalPages = $seoPage->getVar('total_pages');

            if ($page !== 1) {
                $seoPage->setLinkCanonical($articlesManager->getNewsIndexPermalink(array('page' => $page)));
            } else {
                $seoPage->setLinkCanonical($articlesManager->getNewsIndexPermalink());
            }

            if ($totalPages > 1 && $page < $totalPages) {
                $seoPage->setLinkNext($articlesManager->getNewsIndexPermalink(array('page' => $page + 1)));
            }

            if ($totalPages > 1 && $page > 1) {
                $seoPage->setLinkPrev($articlesManager->getNewsIndexPermalink(array('page' => $page - 1)));
            }
        }
    }
}