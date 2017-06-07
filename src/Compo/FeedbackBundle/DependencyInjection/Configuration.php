<?php

namespace Compo\FeedbackBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('compo_feedback');

        $rootNode
            ->children()
            ->arrayNode('types')->prototype('array')->children()
            ->scalarNode('type')->isRequired()->end()
            ->scalarNode('form')->isRequired()->end()
            ->scalarNode('template')->defaultValue('')->end()
            ->scalarNode('description')->defaultValue('')->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
