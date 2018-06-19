<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class TranslatorLoaderProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        try {
            $loaderProvider = $container->findDefinition('sylius.theme.translation.loader_provider');
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds('translation.loader');
        $loaders = [];
        foreach ($taggedServices as $id => $attributes) {
            $loader = $container->findDefinition($id);
            $loader->setLazy(true);

            $loaders[$attributes[0]['alias']] = new Reference($id);

            if (isset($attributes[0]['legacy-alias'])) {
                $loaders[$attributes[0]['legacy-alias']] = new Reference($id);
            }
        }

        $loaderProvider->replaceArgument(0, $loaders);
    }
}
