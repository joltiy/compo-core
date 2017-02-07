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
        $request = $this->get('request');

        $pager = $this->get('compo_articles.manager.articles')->getPager(array(), $request->get('page', 1));

        return $this->render('@CompoArticles/Articles/index.html.twig', array(
            'pager' => $pager,
        ));
    }

    /**
     * Finds and displays a article entity.
     *
     */
    public function showAction(Articles $article)
    {
        return $this->render('@CompoArticles/Articles/show.html.twig', array(
            'article' => $article,
        ));
    }

    public function showBySlugAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $article = $em->getRepository('CompoArticlesBundle:Articles')->findOneBy(array('slug' => $slug));

        return $this->render('@CompoArticles/Articles/show.html.twig', array(
            'article' => $article,
        ));
    }
}
