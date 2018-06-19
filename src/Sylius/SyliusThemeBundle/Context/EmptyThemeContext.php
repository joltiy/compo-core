<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Context;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class EmptyThemeContext implements ThemeContextInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTheme()
    {
        return null;
    }
}
