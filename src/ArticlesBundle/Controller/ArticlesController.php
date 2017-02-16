<?php

namespace Compo\ArticlesBundle\Controller;


use Compo\ArticlesBundle\Entity\Articles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Article controller.
 */
class ArticlesController extends Controller
{
    /**
     * Lists all article entities.
     *
     */
    public function indexAction()
    {

        $compo_articles_settings = $this->get('sylius.settings.manager')->load('compo_articles');

        $seoPage = $this->get('sonata.seo.page');

        $seoPage->addTemplates('articles', array(
            'header' => $compo_articles_settings->get('seo_header'),
            'title' => $compo_articles_settings->get('seo_title'),
            'meta_keyword' => $compo_articles_settings->get('seo_meta_keyword'),
            'meta_description' => $compo_articles_settings->get('seo_meta_description'),
        ));

        $seoPage->build();


        $request = $this->get('request');

        $pager = $this->get('compo_articles.manager.articles')->getPager(array(), $request->get('page', 1));

        return $this->render('@CompoArticles/Articles/index.html.twig', array(
            'pager' => $pager,
        ));
    }

    public function showBySlugAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Articles $article */
        $article = $em->getRepository('CompoArticlesBundle:Articles')->findOneBy(array('slug' => $slug));


        $compo_articles_settings = $this->get('sylius.settings.manager')->load('compo_articles');

        $seoPage = $this->get('sonata.seo.page');

        $seoPage->addTemplates('articles_items', array(
            'header' => $compo_articles_settings->get('seo_items_header'),
            'title' => $compo_articles_settings->get('seo_items_title'),
            'meta_keyword' => $compo_articles_settings->get('seo_items_meta_keyword'),
            'meta_description' => $compo_articles_settings->get('seo_items_meta_description'),
        ));

        $seoPage->addTemplates('article', array(
            'header' => $article->getHeader(),
            'title' => $article->getTitle(),
            'meta_keyword' => $article->getMetaKeyword(),
            'meta_description' => $article->getDescription(),
        ));

        $seoPage->addVar('article', $article);

        $seoPage->build();


        return $this->render('@CompoArticles/Articles/show.html.twig', array(
            'article' => $article,
        ));
    }
}
