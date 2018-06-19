<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Configuration\Filesystem;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface ConfigurationLoaderInterface
{
    /**
     * Loads configuration for given identifier (can be theme name or path to configuration file).
     *
     * @param string $identifier
     *
     * @return array
     */
    public function load($identifier);
}
