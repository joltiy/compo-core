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
     *
     * @param Request $request
     *
     * @throws \Throwable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $manager = $this->get('compo_articles.manager.articles');

        $page = $request->get('page', 1);
        $pager = $manager->getPager([], $page);
        $totalPages = $pager->getPageCount();

        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setContext('article_list');
        $seoPage->addVar('page', $page);
        $seoPage->addVar('total_pages', $totalPages);

        if (1 !== $page) {
            $seoPage->setLinkCanonical($manager->getArticlesIndexPermalink(['page' => $page], 0));
        } else {
            $seoPage->setLinkCanonical($manager->getArticlesIndexPermalink([], 0));
        }

        if ($totalPages > 1 && $page < $totalPages) {
            $seoPage->setLinkNext($manager->getArticlesIndexPermalink(['page' => $page + 1], 0));
        }

        if ($totalPages > 1 && $page > 1) {
            $seoPage->setLinkPrev($manager->getArticlesIndexPermalink(['page' => $page - 1], 0));
        }

        $seoPage->build();

        return $this->render(
            'CompoArticlesBundle:Articles:index.html.twig',
            [
                'pager' => $pager,
            ]
        );
    }

    /**
     * @param $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showByIdAction($id)
    {
        $manager = $this->get('compo_articles.manager.articles');

        $article = $manager->find($id);

        if (!$article) {
            throw $this->createNotFoundException('compo_articles.exception.not_found_article');
        }

        return $this->redirectToRoute('compo_articles_show_by_slug', ['slug' => $article->getSlug()], 301);
    }

    /**
     * @param $slug
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Throwable
     *
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

        $seoPage->addTemplates(
            'article_show',
            [
                'header' => $article->getHeader(),
                'title' => $article->getTitle(),
                'metaKeyword' => $article->getMetaKeyword(),
                'metaDescription' => $article->getMetaDescription(),
            ]
        );

        $seoPage->setLinkCanonical($manager->getArticleShowPermalink($article, 0));

        $seoPage->build();

        return $this->render(
            'CompoArticlesBundle:Articles:show.html.twig',
            [
                'article' => $article,
            ]
        );
    }
}
