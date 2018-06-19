<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\Finder;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface TranslationFilesFinderInterface
{
    /**
     * @param string $path
     *
     * @return array Paths to translation files
     */
    public function findTranslationFiles($path);
}
