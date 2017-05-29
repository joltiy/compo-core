<?php

namespace Compo\NotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $rootNode = $treeBuilder->root('compo_notification');

        $rootNode
            ->children()

            ->arrayNode('events')->prototype('array')->children()

            ->scalarNode('event')->isRequired()->end()
            ->scalarNode('description')->isRequired()->defaultValue('')->end()
            ->scalarNode('recipient')->isRequired()->defaultValue('')->end()
            ->scalarNode('subject')->defaultValue('')->end()
            ->scalarNode('body')->isRequired()->defaultValue('')->end()
            ->scalarNode('help')->defaultValue('')->end()
            ->scalarNode('type')->isRequired()->defaultValue('')->end()

            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
