<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Configuration\Test;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Test\TestConfigurationProvider;
use Sylius\Bundle\ThemeBundle\Configuration\Test\TestThemeConfigurationManagerInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class TestConfigurationProviderSpec extends ObjectBehavior
{
    public function let(TestThemeConfigurationManagerInterface $testThemeConfigurationManager)
    {
        $this->beConstructedWith($testThemeConfigurationManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TestConfigurationProvider::class);
    }

    public function it_implements_configuration_provider_interface()
    {
        $this->shouldImplement(ConfigurationProviderInterface::class);
    }

    public function it_provides_configuration_based_on_test_configuration_manager(TestThemeConfigurationManagerInterface $testThemeConfigurationManager)
    {
        $testThemeConfigurationManager->findAll()->willReturn([
            ['name' => 'theme/name'],
        ]);

        $this->getConfigurations()->shouldReturn([
            ['name' => 'theme/name'],
        ]);
    }
}
