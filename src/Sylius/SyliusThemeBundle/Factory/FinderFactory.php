<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Factory;

use Symfony\Component\Finder\Finder;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class FinderFactory implements FinderFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return Finder::create();
    }
}
