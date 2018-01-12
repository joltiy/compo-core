<?php

namespace Compo\SonataImportBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CompoSonataImportExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $this->prepairConfig($config, $container);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function prepairConfig(array $config, ContainerBuilder $container)
    {
        $container->setParameter(
            'compo_sonata_import.mappings',
            $config['mappings']
        );
        $container->setParameter(
            'compo_sonata_import.upload_dir',
            $config['upload_dir'] ? $config['upload_dir'] :
                $container->get('kernel')->getRootDir() . '/../web/uploads'
        );
        $container->setParameter(
            'compo_sonata_import.class_loaders',
            $config['class_loaders']
        );
        if (!isset($config['encode'])) {
            $config['encode'] = [
                'default' => 'utf8',
                'list' => [],
            ];
        }
        $container->setParameter(
            'compo_sonata_import.encode.default',
            $config['encode']['default']
        );
        $container->setParameter(
            'compo_sonata_import.encode.list',
            $config['encode']['list']
        );
    }
}
