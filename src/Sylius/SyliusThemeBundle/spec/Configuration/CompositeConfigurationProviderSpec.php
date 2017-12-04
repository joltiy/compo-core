<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Configuration;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Configuration\CompositeConfigurationProvider;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class CompositeConfigurationProviderSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(array());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CompositeConfigurationProvider::class);
    }

    public function it_implements_configuration_provider_interface()
    {
        $this->shouldImplement(ConfigurationProviderInterface::class);
    }

    public function it_returns_empty_array_if_no_configurations_are_loaded()
    {
        $this->getConfigurations()->shouldReturn(array());
    }

    public function it_returns_sum_of_configurations_returned_by_nested_configuration_providers(
        ConfigurationProviderInterface $firstConfigurationProvider,
        ConfigurationProviderInterface $secondConfigurationProvider
    ) {
        $this->beConstructedWith(array(
            $firstConfigurationProvider,
            $secondConfigurationProvider,
        ));

        $firstConfigurationProvider->getConfigurations()->willReturn(array(
            array('name' => 'first/theme'),
        ));
        $secondConfigurationProvider->getConfigurations()->willReturn(array(
            array('name' => 'second/theme'),
            array('name' => 'third/theme'),
        ));

        $this->getConfigurations()->shouldReturn(array(
            array('name' => 'first/theme'),
            array('name' => 'second/theme'),
            array('name' => 'third/theme'),
        ));
    }
}
