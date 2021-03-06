<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     *
     * @param Request $request
     *
     * @throws \Throwable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $manager = $this->get('compo_faq.manager.faq');

        $page = $request->get('page', 1);

        $pager = $manager->getPager([], $page);
        $totalPages = $pager->getPageCount();

        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setContext('faq_list');
        $seoPage->addVar('page', $page);
        $seoPage->addVar('total_pages', $pager->getPageCount());

        if (1 !== $page) {
            $seoPage->setLinkCanonical($manager->getFaqIndexPermalink(['page' => $page], 0));
        } else {
            $seoPage->setLinkCanonical($manager->getFaqIndexPermalink([], 0));
        }

        if ($totalPages > 1 && $page < $totalPages) {
            $seoPage->setLinkNext($manager->getFaqIndexPermalink(['page' => $page + 1], 0));
        }

        if ($totalPages > 1 && $page > 1) {
            $seoPage->setLinkPrev($manager->getFaqIndexPermalink(['page' => $page - 1], 0));
        }

        $seoPage->build();

        return $this->render(
            'CompoFaqBundle:Faq:index.html.twig',
            [
                'pager' => $pager,
            ]
        );
    }

    /**
     * @param $slug
     *
     * @throws \Throwable
     *
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
        $seoPage->setContext('faq_show');
        $seoPage->addVar('faq', $article);

        $seoPage->addTemplates(
            'faq_show',
            [
                'header' => $article->getName(),
                'title' => $article->getName(),
                'metaKeyword' => $article->getMetaKeyword(),
                'metaDescription' => $article->getMetaDescription(),
            ]
        );

        $seoPage->setLinkCanonical($manager->getArticleShowPermalink($article, 0));

        $seoPage->build();

        return $this->render(
            'CompoFaqBundle:Faq:show.html.twig',
            [
                'article' => $article,
            ]
        );
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showByIdAction($id)
    {
        $manager = $this->get('compo_faq.manager.faq');

        $article = $manager->find($id);

        if (!$article) {
            throw $this->createNotFoundException('compo_faq.exception.not_found_faq');
        }

        return $this->redirectToRoute('compo_faq_show_by_slug', ['slug' => $article->getSlug()], 301);
    }
}
