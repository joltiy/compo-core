<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\SeoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('compo_seo');

        $rootNode
            ->children()
            ->arrayNode('pages')->prototype('array')->children()
            ->scalarNode('context')->isRequired()->end()
            ->scalarNode('header')->defaultValue('')->end()
            ->scalarNode('title')->defaultValue('')->end()
            ->scalarNode('metaKeyword')->defaultValue('')->end()
            ->scalarNode('metaDescription')->defaultValue('')->end()
            ->scalarNode('description')->defaultValue('')->end()
            ->scalarNode('descriptionAdditional')->defaultValue('')->end()
            ->scalarNode('admin')->defaultValue('')->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
