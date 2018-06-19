<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        return [
            new \Twig_SimpleFilter('parse_url', [$this, 'parseUrl']),
        ];
    }

    /**
     * getUrlHost.
     *
     * @param string $url       url
     * @param        $component
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
