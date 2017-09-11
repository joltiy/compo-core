<?php

namespace Compo\SeoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        /** @noinspection PhpUnusedLocalVariableInspection */
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

            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
