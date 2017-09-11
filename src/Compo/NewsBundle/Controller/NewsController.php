<?php

namespace Compo\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * News controller.
 */
class NewsController extends Controller
{
    /**
     * Lists all news entities.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function indexAction(Request $request)
    {
        $manager = $this->get('compo_news.manager.news');

        $page = $request->get('page', 1);

        $pager = $manager->getPager(array(), $page);

        $totalPages = $pager->getPageCount();

        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setContext('news_list');
        $seoPage->addVar('page', $page);
        $seoPage->addVar('total_pages', $totalPages);

        if ($page !== 1) {
            $seoPage->setLinkCanonical($manager->getNewsIndexPermalink(array('page' => $page), 0));
        } else {
            $seoPage->setLinkCanonical($manager->getNewsIndexPermalink(array(), 0));
        }

        if ($totalPages > 1 && $page < $totalPages) {
            $seoPage->setLinkNext($manager->getNewsIndexPermalink(array('page' => $page + 1), 0));
        }

        if ($totalPages > 1 && $page > 1) {
            $seoPage->setLinkPrev($manager->getNewsIndexPermalink(array('page' => $page - 1), 0));
        }

        $seoPage->build();

        return $this->render('CompoNewsBundle:News:index.html.twig', array(
            'pager' => $pager,
        ));
    }

    /**
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function showBySlugAction($slug)
    {
        $manager = $this->get('compo_news.manager.news');

        $article = $manager->findBySlug($slug);

        if (!$article) {
            throw $this->createNotFoundException('compo_news.exception.not_found_news');
        }

        $manager->increaseViews($article);

        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setContext('news_show');
        $seoPage->addVar('news', $article);

        $seoPage->addTemplates('news_show', array(
            'header' => $article->getHeader(),
            'title' => $article->getTitle(),
            'metaKeyword' => $article->getMetaKeyword(),
            'metaDescription' => $article->getMetaDescription(),
        ));

        $seoPage->setLinkCanonical($manager->getNewsShowPermalink($article, 0));

        $seoPage->build();

        return $this->render('CompoNewsBundle:News:show.html.twig', array(
            'news' => $article,
        ));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showByIdAction($id)
    {
        $manager = $this->get('compo_news.manager.news');

        $article = $manager->find($id);

        if (!$article) {
            throw $this->createNotFoundException('compo_news.exception.not_found_news');
        }

        return $this->redirectToRoute('compo_news_show_by_slug', array('slug' => $article->getSlug()), 302);
    }
}
