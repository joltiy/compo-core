<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\FeedbackBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CompoFeedbackExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $serviceDefintion = $container->getDefinition('compo_feedback.manager.feedback');
        $serviceDefintion->addMethodCall('setTypes', [$config['types']]);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('sonata_block.yml');
        $loader->load('compo_feedback.yml');
        $loader->load('compo_notification.yml');

        $configs = $container->getExtensionConfig('sonata_page');

        $templates = [];

        foreach ($configs as $config) {
            if (isset($config['templates'])) {
                /** @var array $config_templates */
                $config_templates = $config['templates'];
                foreach ($config_templates as $template => $templateConfig) {
                    if (!isset($templates[$template])) {
                        $templates[$template] = [
                            'containers' => [],
                        ];
                    }

                    if (isset($templateConfig['containers'])) {
                        /** @var array $templateConfigContainers */
                        $templateConfigContainers = $templateConfig['containers'];

                        foreach ($templateConfigContainers as $templateContainer => $templateContainerConfig) {
                            if (!isset($templates[$template]['containers'][$templateContainer])) {
                                $templates[$template]['containers'][$templateContainer] = [
                                    'blocks' => [],
                                ];
                            }
                        }
                    }
                }
            }
        }

        $configsSonataBlock = $container->getExtensionConfig('sonata_block');

        foreach ($configsSonataBlock as $item) {
            if (isset($item['blocks'])) {
                /** @var array $item_blocks */
                $item_blocks = $item['blocks'];
                foreach ($item_blocks as $block => $blockConfig) {
                    if (!isset($blockConfig['contexts'])) {
                        continue;
                    }

                    if (!\in_array('sonata_page_bundle', $blockConfig['contexts'], true)) {
                        continue;
                    }

                    foreach ($templates as $templateName => $templateConfig) {
                        /** @var array $templateConfigContainers */
                        $templateConfigContainers = $templateConfig['containers'];
                        foreach ($templateConfigContainers as $containerName => $containerConfig) {
                            $templates[$templateName]['containers'][$containerName]['blocks'][] = $block;
                        }
                    }
                }
            }
        }

        $container->prependExtensionConfig('sonata_page', ['templates' => $templates]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'compo_feedback';
    }
}
