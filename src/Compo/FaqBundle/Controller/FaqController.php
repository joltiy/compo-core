<?php

namespace Compo\FaqBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Article controller.
 */
class FaqController extends Controller
{
    /**
     * Lists all article entities.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $manager = $this->get('compo_faq.manager.faq');

        $page = $request->get('page', 1);

        $pager = $manager->getPager(array(), $page);

        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setContext('compo_faq');
        $seoPage->addVar('page', $page);
        $seoPage->addVar('total_pages', $pager->getPageCount());
        $seoPage->build();

        return $this->render('CompoFaqBundle:Faq:index.html.twig', array(
            'pager' => $pager,
        ));
    }

    /**
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showBySlugAction($slug)
    {
        $manager = $this->get('compo_faq.manager.faq');

        $article = $manager->findBySlug($slug);

        if (!$article) {
            throw $this->createNotFoundException('compo_faq.exception.not_found_article');
        }

        $manager->increaseViews($article);

        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setContext('compo_faq');
        $seoPage->addVar('article', $article);
        $seoPage->build();

        return $this->render('CompoFaqBundle:Faq:show.html.twig', array(
            'article' => $article,
        ));
    }
}
