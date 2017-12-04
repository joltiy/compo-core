<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Configuration\Filesystem;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProcessorInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\ConfigurationLoaderInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\ProcessingConfigurationLoader;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ProcessingConfigurationLoaderSpec extends ObjectBehavior
{
    public function let(ConfigurationLoaderInterface $decoratedLoader, ConfigurationProcessorInterface $configurationProcessor)
    {
        $this->beConstructedWith($decoratedLoader, $configurationProcessor);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ProcessingConfigurationLoader::class);
    }

    public function it_implements_loader_interface()
    {
        $this->shouldImplement(ConfigurationLoaderInterface::class);
    }

    public function it_processes_the_configuration(
        ConfigurationLoaderInterface $decoratedLoader,
        ConfigurationProcessorInterface $configurationProcessor
    ) {
        $basicConfiguration = array('name' => 'example/sylius-theme');

        $decoratedLoader->load('theme-configuration-resource')->willReturn($basicConfiguration);

        $configurationProcessor->process(array($basicConfiguration))->willReturn(array(
            'name' => 'example/sylius-theme',
        ));

        $this->load('theme-configuration-resource')->shouldReturn(array(
            'name' => 'example/sylius-theme',
        ));
    }

    public function it_processes_the_configuration_and_extracts_extra_sylius_theme_key_as_another_configuration(
        ConfigurationLoaderInterface $decoratedLoader,
        ConfigurationProcessorInterface $configurationProcessor
    ) {
        $basicConfiguration = array(
            'name' => 'example/sylius-theme',
            'extra' => array(
                'sylius-theme' => array(
                    'name' => 'example/brand-new-sylius-theme',
                ),
            ),
        );

        $decoratedLoader->load('theme-configuration-resource')->willReturn($basicConfiguration);

        $configurationProcessor->process(array(
            $basicConfiguration,
            array('name' => 'example/brand-new-sylius-theme'),
        ))->willReturn(array(
            'name' => 'example/brand-new-sylius-theme',
        ));

        $this->load('theme-configuration-resource')->shouldReturn(array(
            'name' => 'example/brand-new-sylius-theme',
        ));
    }
}
