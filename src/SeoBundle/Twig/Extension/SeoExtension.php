<?php

namespace Compo\SeoBundle\Twig\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

/**
 * {@inheritDoc}
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
            new \Twig_SimpleFunction('sonata_seo_link_next', array($this, 'getLinkNext'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('sonata_seo_link_prev', array($this, 'getLinkPrev'), array('is_safe' => array('html'))),
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

}
