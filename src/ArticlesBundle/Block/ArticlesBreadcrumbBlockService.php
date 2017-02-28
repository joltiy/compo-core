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
    public function getName()
    {
        return 'compo_articles.block.breadcrumb';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $menu = $this->getRootMenu($blockContext);

        $block = $blockContext->getBlock();

        $article = $block->getSetting('article');

        if ($article) {
            $menu->addChild('articles', array(
                'uri' => $this->getContainer()->get('router')->generate('compo_articles_index'),
                'label' => 'breadcrumb.articles',
                'extras' => array(
                    'translation_domain' => 'CompoArticlesBundle'
                )
            ));

            $menu->addChild($article->getSlug(), array(
                'label' => $article->getName()
            ));
        } else {
            $menu->addChild('all', array(
                'label' => 'breadcrumb.articles',
                'extras' => array(
                    'translation_domain' => 'CompoArticlesBundle'
                )
            ));
        }

        return $menu;
    }
}