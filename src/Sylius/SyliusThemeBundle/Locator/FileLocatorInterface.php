<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Locator;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface FileLocatorInterface
{
    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException If name is not valid or file was not found
     *
     * @return string
     */
    public function locateFileNamed($name);

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException If name is not valid or files were not found
     *
     * @return array
     */
    public function locateFilesNamed($name);
}
