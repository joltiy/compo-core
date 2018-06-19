<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Configuration\Filesystem;

use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProcessorInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ProcessingConfigurationLoader implements ConfigurationLoaderInterface
{
    /**
     * @var ConfigurationLoaderInterface
     */
    private $decoratedLoader;

    /**
     * @var ConfigurationProcessorInterface
     */
    private $configurationProcessor;

    /**
     * @param ConfigurationLoaderInterface    $decoratedLoader
     * @param ConfigurationProcessorInterface $configurationProcessor
     */
    public function __construct(ConfigurationLoaderInterface $decoratedLoader, ConfigurationProcessorInterface $configurationProcessor)
    {
        $this->decoratedLoader = $decoratedLoader;
        $this->configurationProcessor = $configurationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function load($identifier)
    {
        $rawConfiguration = $this->decoratedLoader->load($identifier);

        $configurations = [$rawConfiguration];
        if (isset($rawConfiguration['extra']['sylius-theme'])) {
            $configurations[] = $rawConfiguration['extra']['sylius-theme'];
        }

        return $this->configurationProcessor->process($configurations);
    }
}
