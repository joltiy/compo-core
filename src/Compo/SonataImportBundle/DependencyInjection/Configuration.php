<?php

namespace Compo\SonataImportBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('compo_sonata_import');

        $rootNode
            ->children()
                ->arrayNode('mappings')
                    ->defaultValue([])
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('class')->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('upload_dir')
                    ->defaultValue(null)
                ->end()
                ->arrayNode('class_loaders')
                    ->defaultValue([[
                        'name' => 'CSV',
                        'class' => 'Compo\SonataImportBundle\Loaders\CsvFileLoader',
                    ]])
                        ->prototype('array')
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('class')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('encode')
                    ->children()
                        ->scalarNode('default')->defaultValue('utf8')->end()
                        ->arrayNode('list')->defaultValue([])
                            ->prototype('scalar')
                            ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
