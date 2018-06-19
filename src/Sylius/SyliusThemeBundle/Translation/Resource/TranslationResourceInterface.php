<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\Resource;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface TranslationResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @return string
     */
    public function getFormat();

    /**
     * @return string
     */
    public function getDomain();
}
