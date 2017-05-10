<?php

namespace Compo\ArticlesBundle\Block;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\SeoBundle\Block\Breadcrumb\BaseBreadcrumbMenuBlockService;

/**
 * {@inheritdoc}
 */
class ArticlesBreadcrumbBlockService extends BaseBreadcrumbMenuBlockService
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $container = $this->getContainer();

        $router = $container->get('router');

        $block = $blockContext->getBlock();

        $menu = $this->getRootMenu($blockContext);

        $article = $block->getSetting('article');

        if ($article) {
            $menu->addChild('breadcrumb.articles', array(
                'uri' => $router->generate('compo_articles_index'),
                'label' => 'breadcrumb.articles',
                'extras' => array(
                    'translation_domain' => 'CompoArticlesBundle'
                )
            ));

            $menu->addChild($article->getSlug(), array(
                'label' => $article->getName()
            ));
        } else {
            $menu->addChild('breadcrumb.articles', array(
                'label' => 'breadcrumb.articles',
                'extras' => array(
                    'translation_domain' => 'CompoArticlesBundle'
                )
            ));
        }

        return $menu;
    }
}