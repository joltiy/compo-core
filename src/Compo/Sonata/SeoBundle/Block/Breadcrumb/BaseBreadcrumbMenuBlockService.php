<?php

namespace Compo\Sonata\SeoBundle\Block\Breadcrumb;


use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritDoc}
 */
class BaseBreadcrumbMenuBlockService extends \Sonata\SeoBundle\Block\Breadcrumb\BaseBreadcrumbMenuBlockService
{
    use ContainerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        parent::configureSettings($resolver);

        $resolver->setDefaults(
            array(
                'current_uri' => $this->getRequest()->getRequestUri(),
                'menu_template' => 'SonataSeoBundle:Block:breadcrumb.html.twig',
                'include_homepage_link' => true,
                'context' => false,
            )
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
        /*
         * @todo : Use the router to get the homepage URI
         */

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


        if ($settings['include_homepage_link']) {
            $menu->addChild('sonata_seo_homepage_breadcrumb', array('uri' => '/'));
        }

        return $menu;
    }
}
