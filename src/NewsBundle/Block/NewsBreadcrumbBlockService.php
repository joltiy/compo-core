<?php

namespace Compo\NewsBundle\Block;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Knp\Menu\ItemInterface;
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
    public function getName()
    {
        return 'compo_news.block.breadcrumb';
    }

    /**
     * Gets the menu to render.
     *
     * @param BlockContextInterface $blockContext
     *
     * @return ItemInterface|string
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $menu = $this->getRootMenu($blockContext);

        $block = $blockContext->getBlock();

        $news = $block->getSetting('news');

        if ($news) {
            $menu->addChild('news', array(
                'uri' => $this->getContainer()->get('router')->generate('compo_news_index'),
                'label' => 'breadcrumb.news',
                'extras' => array(
                    'translation_domain' => 'CompoNewsBundle'
                )
            ));

            $menu->addChild($news->getSlug(), array(
                'label' => $news->getName()
            ));
        } else {
            $menu->addChild('all', array(
                'label' => 'breadcrumb.news',
                'extras' => array(
                    'translation_domain' => 'CompoNewsBundle'
                )
            ));
        }

        return $menu;
    }
}