<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Loader;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface CircularDependencyCheckerInterface
{
    /**
     * @param ThemeInterface $theme
     *
     * @throws CircularDependencyFoundException
     */
    public function check(ThemeInterface $theme);
}
