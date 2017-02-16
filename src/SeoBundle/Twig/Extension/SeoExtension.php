<?php

namespace Compo\SeoBundle\Twig\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

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
    public function getHeader()
    {
        $seo_page = $this->getContainer()->get('sonata.seo.page');

        return $seo_page->getHeader();
    }
}
