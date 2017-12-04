<?php

namespace Compo\CoreBundle\Twig;

use Twig_Extension;

/**
 * Class ShowMoreExtension.
 */
class UrlExtension extends Twig_Extension
{
    /**
     * getFilters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('parse_url', array($this, 'parseUrl')),
        );
    }

    /**
     * getUrlHost.
     *
     * @param string $url url
     *
     * @return string
     */
    public function parseUrl($url, $component)
    {
        $componentList = parse_url($url);

        if (isset($componentList[$component])) {
            return $componentList[$component];
        }

        return false;
    }

    /**
     * getName.
     *
     * @return string
     */
    public function getName()
    {
        return 'url_extension';
    }
}
