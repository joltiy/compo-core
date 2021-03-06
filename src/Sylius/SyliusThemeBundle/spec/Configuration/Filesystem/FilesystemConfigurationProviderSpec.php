<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Configuration\Filesystem;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\ConfigurationLoaderInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\FilesystemConfigurationProvider;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class FilesystemConfigurationProviderSpec extends ObjectBehavior
{
    public function let(FileLocatorInterface $fileLocator, ConfigurationLoaderInterface $loader)
    {
        $this->beConstructedWith($fileLocator, $loader, 'configurationfile.json');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(FilesystemConfigurationProvider::class);
    }

    public function it_implements_configuration_provider_interface()
    {
        $this->shouldImplement(ConfigurationProviderInterface::class);
    }

    public function it_provides_loaded_configuration_files(FileLocatorInterface $fileLocator, ConfigurationLoaderInterface $loader)
    {
        $fileLocator->locateFilesNamed('configurationfile.json')->willReturn([
            '/cristopher/configurationfile.json',
            '/richard/configurationfile.json',
        ]);

        $loader->load('/cristopher/configurationfile.json')->willReturn(['name' => 'cristopher/sylius-theme']);
        $loader->load('/richard/configurationfile.json')->willReturn(['name' => 'richard/sylius-theme']);

        $this->getConfigurations()->shouldReturn([
            ['name' => 'cristopher/sylius-theme'],
            ['name' => 'richard/sylius-theme'],
        ]);
    }

    public function it_provides_an_empty_array_if_there_were_no_themes_found(FileLocatorInterface $fileLocator)
    {
        $fileLocator->locateFilesNamed('configurationfile.json')->willThrow(\InvalidArgumentException::class);

        $this->getConfigurations()->shouldReturn([]);
    }
}
