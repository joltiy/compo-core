<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\Provider\Loader;

use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class TranslatorLoaderProvider implements TranslatorLoaderProviderInterface
{
    /**
     * @var LoaderInterface[]
     */
    private $loaders;

    /**
     * @param LoaderInterface[] $loaders
     */
    public function __construct(array $loaders = [])
    {
        $this->loaders = $loaders;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoaders()
    {
        return $this->loaders;
    }
}
