<?php

namespace Compo\FaqBundle\Seo;

use Compo\FaqBundle\Entity\Faq;
use Compo\SeoBundle\Service\BaseService;

/**
 * {@inheritDoc}
 */
class FaqSeoService extends BaseService
{
    /**
     * {@inheritDoc}
     */
    public function build()
    {
        $container = $this->getContainer();

        $seoPage = $this->getSeoPage();

        $faqManager = $container->get('compo_faq.manager.faq');
        $settingsManager = $container->get('sylius.settings_manager');

        $faqSettings = $settingsManager->load('compo_faq');

        /** @var Faq $article */
        $article = $seoPage->getVar('article');

        if ($article) {
            $seoPage->addTemplates('seo_items', array(
                'header' => $faqSettings->get('seo_items_header'),
                'title' => $faqSettings->get('seo_items_title'),
                'meta_keyword' => $faqSettings->get('seo_items_meta_keyword'),
                'meta_description' => $faqSettings->get('seo_items_meta_description'),
            ));

            $seoPage->addTemplates('article', array(
                'header' => $article->getHeader(),
                'title' => $article->getTitle(),
                'meta_keyword' => $article->getMetaKeyword(),
                'meta_description' => $article->getMetaDescription(),
            ));

            $seoPage->setLinkCanonical($faqManager->getArticleShowPermalink($article));
        } else {
            $seoPage->addTemplates('seo_index', array(
                'header' => $faqSettings->get('seo_index_header'),
                'description' => $faqSettings->get('seo_index_description'),
                'title' => $faqSettings->get('seo_index_title'),
                'meta_keyword' => $faqSettings->get('seo_index_meta_keyword'),
                'meta_description' => $faqSettings->get('seo_index_meta_description'),
            ));

            $page = $seoPage->getVar('page');
            $totalPages = $seoPage->getVar('total_pages');

            if ($page !== 1) {
                $seoPage->setLinkCanonical($faqManager->getFaqIndexPermalink(array('page' => $page)));
            } else {
                $seoPage->setLinkCanonical($faqManager->getFaqIndexPermalink());
            }

            if ($totalPages > 1 && $page < $totalPages) {
                $seoPage->setLinkNext($faqManager->getFaqIndexPermalink(array('page' => $page + 1)));
            }

            if ($totalPages > 1 && $page > 1) {
                $seoPage->setLinkPrev($faqManager->getFaqIndexPermalink(array('page' => $page - 1)));
            }
        }
    }
}