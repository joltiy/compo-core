<?php

namespace Compo\ArticlesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Article controller.
 */
class ArticlesController extends Controller
{
    /**
     * Lists all article entities.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $manager = $this->get('compo_articles.manager.articles');
        $seoPage = $this->get('sonata.seo.page');
        $request = $this->get('request');

        $page = $request->get('page', 1);

        $pager = $manager->getPager(array(), $page);

        $seoPage->setContext('compo_articles');
        $seoPage->addVar('page', $page);
        $seoPage->addVar('total_pages', $pager->getPageCount());

        $seoPage->build();

        return $this->render('@CompoArticles/Articles/index.html.twig', array(
            'pager' => $pager,
        ));
    }

    /**
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showBySlugAction($slug)
    {
        $manager = $this->get('compo_articles.manager.articles');

        $article = $manager->findBySlug($slug);

        if (!$article) {
            throw $this->createNotFoundException('compo_articles.exception.not_found_article');
        }

        $manager->increaseViews($article);

        $seoPage = $this->get('sonata.seo.page');

        $seoPage->setContext('compo_articles');
        $seoPage->addVar('article', $article);
        $seoPage->build();

        return $this->render('@CompoArticles/Articles/show.html.twig', array(
            'article' => $article,
        ));
    }
}
