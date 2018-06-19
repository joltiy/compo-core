<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ImportBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CompoImportExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $this->prepareConfig($config, $container);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function prepareConfig(array $config, ContainerBuilder $container)
    {
        $container->setParameter(
            'compo_import.upload_dir',
            $config['upload_dir']
        );
        $container->setParameter(
            'compo_import.class_loaders',
            $config['class_loaders']
        );
        if (!isset($config['encode'])) {
            $config['encode'] = [
                'default' => 'utf8',
                'list' => ['utf8'],
            ];
        }
        $container->setParameter(
            'compo_import.encode.default',
            $config['encode']['default']
        );
        $container->setParameter(
            'compo_import.encode.list',
            $config['encode']['list']
        );
    }
}
