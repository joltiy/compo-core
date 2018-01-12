<?php

namespace Compo\NewsBundle\Block;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\SeoBundle\Block\Breadcrumb\BaseBreadcrumbMenuBlockService;

/**
 * {@inheritdoc}
 */
class NewsBreadcrumbBlockService extends BaseBreadcrumbMenuBlockService
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $menu = $this->getRootMenu($blockContext);

        $block = $blockContext->getBlock();

        $news = $block->getSetting('news');

        if ($news) {
            $menu->addChild(
                'news',
                [
                    'uri' => $this->getContainer()->get('router')->generate('compo_news_index'),
                    'label' => 'breadcrumb.news',
                    'extras' => [
                        'translation_domain' => 'CompoNewsBundle',
                    ],
                ]
            );

            $menu->addChild(
                $news->getSlug(),
                [
                    'label' => $news->getName(),
                ]
            );
        } else {
            $menu->addChild(
                'all',
                [
                    'label' => 'breadcrumb.news',
                    'extras' => [
                        'translation_domain' => 'CompoNewsBundle',
                    ],
                ]
            );
        }

        return $menu;
    }
}
