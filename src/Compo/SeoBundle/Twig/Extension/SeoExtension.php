<?php

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
        return array(
            new \Twig_SimpleFunction('compo_seo_header', array($this, 'getHeader'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('compo_seo_description', array($this, 'getDescription'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('compo_seo_description_additional', array($this, 'getDescriptionAdditional'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('sonata_seo_link_next', array($this, 'getLinkNext'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('sonata_seo_link_prev', array($this, 'getLinkPrev'), array('is_safe' => array('html'))),

            new \Twig_SimpleFunction('compo_core_admin_navbar', array($this, 'getAdminNavBar'), array('is_safe' => array('html'))),
        );
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

        return $seo_page->getDescription();
    }

    /**
     * @return string
     */
    public function getDescriptionAdditional()
    {
        $seo_page = $this->getContainer()->get('sonata.seo.page');

        return $seo_page->getDescriptionAdditional();
    }

    public function getAdminNavBar()
    {
        $seo_page = $this->getContainer()->get('sonata.seo.page');

        $context = $seo_page->getContext();

        if (!$context) {
            return;
        }

        $seoPageManager = $this->getContainer()->get('compo_seo.page.manager');

        $item = $seoPageManager->getSeoPageItem($context);

        if (!$item) {
            return;
        }

        if (!$item['admin']) {
            return;
        }

        $admin = $this->getContainer()->get($item['admin']);

        $menu = $admin->configureAdminNavBar($context, $seo_page->getVars());

        if ($menu) {
            $menuRenderer = new \Knp\Menu\Renderer\TwigRenderer($this->getContainer()->get('twig'), 'knp_menu.html.twig', new \Knp\Menu\Matcher\Matcher());

            return $menuRenderer->render($menu, array(
                'template' => 'CompoCoreBundle:Menu:knp_menu_admin.html.twig',
            ));
        }
    }
}
