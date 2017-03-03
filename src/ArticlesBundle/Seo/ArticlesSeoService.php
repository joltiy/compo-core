<?php

namespace Compo\ArticlesBundle\Seo;

use Compo\ArticlesBundle\Entity\Articles;
use Compo\SeoBundle\Service\BaseService;

/**
 * {@inheritDoc}
 */
class ArticlesSeoService extends BaseService
{
    /**
     * {@inheritDoc}
     */
    public function build()
    {
        $container = $this->getContainer();

        $seoPage = $this->getSeoPage();

        $articlesManager = $container->get('compo_articles.manager.articles');
        $settingsManager = $container->get('sylius.settings.manager');

        $articlesSettings = $settingsManager->load('compo_articles');

        /** @var Articles $article */
        $article = $seoPage->getVar('article');

        if ($article) {
            $seoPage->addTemplates('seo_items', array(
                'header' => $articlesSettings->get('seo_items_header'),
                'title' => $articlesSettings->get('seo_items_title'),
                'meta_keyword' => $articlesSettings->get('seo_items_meta_keyword'),
                'meta_description' => $articlesSettings->get('seo_items_meta_description'),
            ));

            $seoPage->addTemplates('article', array(
                'header' => $article->getHeader(),
                'title' => $article->getTitle(),
                'meta_keyword' => $article->getMetaKeyword(),
                'meta_description' => $article->getMetaDescription(),
            ));

            $seoPage->setLinkCanonical($articlesManager->getArticleShowPermalink($article));
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
                $seoPage->setLinkCanonical($articlesManager->getArticlesIndexPermalink(array('page' => $page)));
            } else {
                $seoPage->setLinkCanonical($articlesManager->getArticlesIndexPermalink());
            }


            if ($totalPages > 1 && $page < $totalPages) {
                $seoPage->setLinkNext($articlesManager->getArticlesIndexPermalink(array('page' => $page + 1)));
            }

            if ($totalPages > 1 && $page > 1) {
                $seoPage->setLinkPrev($articlesManager->getArticlesIndexPermalink(array('page' => $page - 1)));
            }
        }
    }
}