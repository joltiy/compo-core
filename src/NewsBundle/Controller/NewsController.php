<?php

namespace Compo\NewsBundle\Controller;

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
        $manager = $this->get('compo_news.manager.news');
        $seoPage = $this->get('sonata.seo.page');
        $request = $this->get('request');

        $page = $request->get('page', 1);

        $pager = $manager->getPager(array(), $page);

        $seoPage->setContext('compo_news');
        $seoPage->addVar('page', $page);
        $seoPage->addVar('total_pages', $pager->getPageCount());

        $seoPage->build();

        return $this->render('CompoNewsBundle:News:index.html.twig', array(
            'pager' => $pager,
        ));
    }

    /**
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
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

        $seoPage->setContext('compo_news');
        $seoPage->addVar('news', $article);
        $seoPage->build();

        return $this->render('CompoNewsBundle:News:show.html.twig', array(
            'news' => $article,
        ));
    }
}
