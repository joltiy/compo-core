<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\Provider\Loader;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface TranslatorLoaderProviderInterface
{
    /**
     * @return array Format => Loader
     */
    public function getLoaders();
}
