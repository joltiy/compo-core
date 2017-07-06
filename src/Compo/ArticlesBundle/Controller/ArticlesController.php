<?php

namespace Compo\ArticlesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Article controller.
 */
class ArticlesController extends Controller
{
    /**
     * Lists all article entities.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $manager = $this->get('compo_articles.manager.articles');

        $page = $request->get('page', 1);

        $pager = $manager->getPager(array(), $page);

        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setContext('compo_articles');
        $seoPage->addVar('page', $page);
        $seoPage->addVar('total_pages', $pager->getPageCount());
        $seoPage->build();


        return $this->render('CompoArticlesBundle:Articles:index.html.twig', array(
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

        return $this->render('CompoArticlesBundle:Articles:show.html.twig', array(
            'article' => $article,
        ));
    }
}
