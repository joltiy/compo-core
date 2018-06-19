<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $rootNode = $treeBuilder->root('compo_notification');

        $rootNode
            ->children()
            ->arrayNode('events')->prototype('array')->children()
            ->scalarNode('event')->isRequired()->end()
            ->scalarNode('name')->isRequired()->end()
            ->scalarNode('recipient')->isRequired()->defaultValue('')->end()
            ->scalarNode('recipient_sms')->defaultValue('')->end()
            ->scalarNode('subject')->defaultValue('')->end()
            ->scalarNode('body')->isRequired()->defaultValue('')->end()
            ->scalarNode('body_sms')->defaultValue('')->end()
            ->scalarNode('help')->defaultValue('')->end()
            ->scalarNode('type')->isRequired()->defaultValue('')->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
