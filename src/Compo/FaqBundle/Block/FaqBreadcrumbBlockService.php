<?php

namespace Compo\FaqBundle\Block;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\SeoBundle\Block\Breadcrumb\BaseBreadcrumbMenuBlockService;

/**
 * {@inheritdoc}
 */
class FaqBreadcrumbBlockService extends BaseBreadcrumbMenuBlockService
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
            $menu->addChild(
                'breadcrumb.faq',
                [
                    'uri' => $router->generate('compo_faq_index'),
                    'label' => 'breadcrumb.faq',
                    'extras' => [
                        'translation_domain' => 'CompoFaqBundle',
                    ],
                ]
            );

            $menu->addChild(
                $article->getSlug(),
                [
                    'label' => $article->getName(),
                ]
            );
        } else {
            $menu->addChild(
                'breadcrumb.faq',
                [
                    'label' => 'breadcrumb.faq',
                    'extras' => [
                        'translation_domain' => 'CompoFaqBundle',
                    ],
                ]
            );
        }

        return $menu;
    }
}
