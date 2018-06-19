<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Configuration;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class CompositeConfigurationProvider implements ConfigurationProviderInterface
{
    /**
     * @var ConfigurationProviderInterface[]
     */
    private $configurationProviders;

    /**
     * @param ConfigurationProviderInterface[] $configurationProviders
     */
    public function __construct(array $configurationProviders)
    {
        $this->configurationProviders = $configurationProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurations()
    {
        $configurations = [];
        foreach ($this->configurationProviders as $configurationProvider) {
            $configurations = array_merge(
                $configurations,
                $configurationProvider->getConfigurations()
            );
        }

        return $configurations;
    }
}
