<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Factory;

use Sylius\Bundle\ThemeBundle\Model\ThemeScreenshot;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface ThemeScreenshotFactoryInterface
{
    /**
     * @param array $data
     *
     * @return ThemeScreenshot
     */
    public function createFromArray(array $data);
}
