<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\SeoBundle\Twig\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

/**
 * {@inheritdoc}
 */
class SeoExtension extends \Twig_Extension
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('compo_seo_header', [$this, 'getHeader'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('compo_seo_description', [$this, 'getDescription'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('compo_seo_description_additional', [$this, 'getDescriptionAdditional'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('sonata_seo_link_next', [$this, 'getLinkNext'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('sonata_seo_link_prev', [$this, 'getLinkPrev'], ['is_safe' => ['html']]),

            new \Twig_SimpleFunction('compo_core_admin_navbar', [$this, 'getAdminNavBar'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'compo_seo';
    }

    /**
     * @return string
     */
    public function getLinkNext()
    {
        $seo_page = $this->getContainer()->get('sonata.seo.page');

        if ($seo_page->getLinkNext()) {
            return sprintf("<link rel=\"next\" href=\"%s\"/>\n", $seo_page->getLinkNext());
        }

        return '';
    }

    /**
     * @return string
     */
    public function getLinkPrev()
    {
        $seo_page = $this->getContainer()->get('sonata.seo.page');

        if ($seo_page->getLinkPrev()) {
            return sprintf("<link rel=\"prev\" href=\"%s\"/>\n", $seo_page->getLinkPrev());
        }

        return '';
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        $seo_page = $this->getContainer()->get('sonata.seo.page');

        return $seo_page->getHeader();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $seo_page = $this->getContainer()->get('sonata.seo.page');

        $vars = $seo_page->getVars();

        if (isset($vars['page']) && $vars['page'] > 1) {
            return '';
        }

        return $seo_page->getDescription();
    }

    /**
     * @return string
     */
    public function getDescriptionAdditional()
    {
        $seo_page = $this->getContainer()->get('sonata.seo.page');

        $vars = $seo_page->getVars();

        if (isset($vars['page']) && $vars['page'] > 1) {
            return '';
        }

        return $seo_page->getDescriptionAdditional();
    }

    /**
     * @return null|string
     */
    public function getAdminNavBar()
    {
        $seo_page = $this->getContainer()->get('sonata.seo.page');

        $context = $seo_page->getContext();

        if (!$context) {
            return null;
        }

        $seoPageManager = $this->getContainer()->get('compo_seo.page.manager');

        $item = $seoPageManager->getSeoPageItem($context);

        if (!$item) {
            return null;
        }

        if (!$item['admin']) {
            return null;
        }

        $admin = $this->getContainer()->get($item['admin']);

        $menu = $admin->configureAdminNavBar($context, $seo_page->getVars());

        if ($menu) {
            $menuRenderer = new \Knp\Menu\Renderer\TwigRenderer($this->getContainer()->get('twig'), 'knp_menu.html.twig', new \Knp\Menu\Matcher\Matcher());

            return $menuRenderer->render($menu, [
                'template' => 'CompoCoreBundle:Menu:knp_menu_admin.html.twig',
            ]);
        }

        return null;
    }
}
