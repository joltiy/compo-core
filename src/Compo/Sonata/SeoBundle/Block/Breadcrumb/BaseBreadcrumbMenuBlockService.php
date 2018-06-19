<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\SeoBundle\Block\Breadcrumb;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Хлебная крошка на главной.
 */
class BaseBreadcrumbMenuBlockService extends \Sonata\SeoBundle\Block\Breadcrumb\BaseBreadcrumbMenuBlockService
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        parent::configureSettings($resolver);

        $resolver->setDefaults(
            [
                'current_uri' => $this->getRequest()->getRequestUri(),
                'menu_template' => 'SonataSeoBundle:Block:breadcrumb.html.twig',
                'include_homepage_link' => true,
                'context' => false,
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
    }

    /**
     * Initialize breadcrumb menu.
     *
     * @param BlockContextInterface $blockContext
     *
     * @return \Knp\Menu\ItemInterface
     */
    protected function getRootMenu(BlockContextInterface $blockContext)
    {
        $settings = $blockContext->getSettings();

        $menu = $this->getFactory()->createItem('breadcrumb');

        $menu->setChildrenAttribute('class', 'breadcrumb');

        if (!$settings['current_uri']) {
            $settings['current_uri'] = $this->getRequest()->getRequestUri();
        }

        if (method_exists($menu, 'setCurrentUri')) {
            $menu->setCurrentUri($settings['current_uri']);
        }

        if (method_exists($menu, 'setCurrent')) {
            $menu->setCurrent($settings['current_uri']);
        }

        $uri = $this->getContainer()->get('router')->generate(
            'page_slug',
            ['path' => '/']
        );

        if ($settings['include_homepage_link']) {
            $menu->addChild('sonata_seo_homepage_breadcrumb', ['uri' => $uri]);
        }

        return $menu;
    }
}
