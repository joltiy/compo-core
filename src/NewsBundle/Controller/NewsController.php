<?php

namespace Compo\NewsBundle\Controller;

use Compo\NewsBundle\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * News controller.
 */
class NewsController extends Controller
{
    /**
     * Lists all news entities.
     */
    public function indexAction()
    {
        $compo_news_settings = $this->get('sylius.settings.manager')->load('compo_news');

        $seoPage = $this->get('sonata.seo.page');

        $seoPage->addTemplates('news', array(
            'header' => $compo_news_settings->get('seo_header'),
            'title' => $compo_news_settings->get('seo_title'),
            'meta_keyword' => $compo_news_settings->get('seo_meta_keyword'),
            'meta_description' => $compo_news_settings->get('seo_meta_description'),
        ));

        $seoPage->build();

        $request = $this->get('request');

        $pager = $this->get('compo_news.manager.news')->getPager(array(), $request->get('page', 1));

        return $this->render('@CompoNews/News/index.html.twig', array(
            'pager' => $pager,
        ));
    }

    /**
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showBySlugAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var News $news */
        $news = $em->getRepository('CompoNewsBundle:News')->findBySlug($slug);

        if (!$news) {
            throw $this->createNotFoundException('compo_news.exception.not_found_news');
        }

        $compo_news_settings = $this->get('sylius.settings.manager')->load('compo_news');

        $seoPage = $this->get('sonata.seo.page');

        $seoPage->addTemplates('news_items', array(
            'header' => $compo_news_settings->get('seo_items_header'),
            'title' => $compo_news_settings->get('seo_items_title'),
            'meta_keyword' => $compo_news_settings->get('seo_items_meta_keyword'),
            'meta_description' => $compo_news_settings->get('seo_items_meta_description'),
        ));

        $seoPage->addTemplates('news', array(
            'header' => $news->getHeader(),
            'title' => $news->getTitle(),
            'meta_keyword' => $news->getMetaKeyword(),
            'meta_description' => $news->getMetaDescription(),
        ));

        $seoPage->addVar('news', $news);

        $seoPage->build();

        return $this->render('@CompoNews/News/show.html.twig', array(
            'news' => $news,
        ));
    }
}