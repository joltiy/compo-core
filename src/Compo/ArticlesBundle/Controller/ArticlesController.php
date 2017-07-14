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
        $totalPages = $pager->getPageCount();

        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setContext('article_list');
        $seoPage->addVar('page', $page);
        $seoPage->addVar('total_pages', $totalPages);

        if ($page !== 1) {
            $seoPage->setLinkCanonical($manager->getArticlesIndexPermalink(array('page' => $page), 0));
        } else {
            $seoPage->setLinkCanonical($manager->getArticlesIndexPermalink(array(), 0));
        }

        if ($totalPages > 1 && $page < $totalPages) {
            $seoPage->setLinkNext($manager->getArticlesIndexPermalink(array('page' => $page + 1), 0));
        }

        if ($totalPages > 1 && $page > 1) {
            $seoPage->setLinkPrev($manager->getArticlesIndexPermalink(array('page' => $page - 1), 0));
        }

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
        $seoPage->setContext('article_show');
        $seoPage->addVar('article', $article);

        $seoPage->addTemplates('article_show', array(
            'header' => $article->getHeader(),
            'title' => $article->getTitle(),
            'metaKeyword' => $article->getMetaKeyword(),
            'metaDescription' => $article->getMetaDescription(),
        ));

        $seoPage->setLinkCanonical($manager->getArticleShowPermalink($article, 0));


        $seoPage->build();

        return $this->render('CompoArticlesBundle:Articles:show.html.twig', array(
            'article' => $article,
        ));
    }
}
