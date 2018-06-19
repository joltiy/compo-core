<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Locator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Locator\ApplicationResourceLocator;
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ApplicationResourceLocatorSpec extends ObjectBehavior
{
    public function let(Filesystem $filesystem)
    {
        $this->beConstructedWith($filesystem);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ApplicationResourceLocator::class);
    }

    public function it_implements_resource_locator_interface()
    {
        $this->shouldImplement(ResourceLocatorInterface::class);
    }

    public function it_locates_application_resource(Filesystem $filesystem, ThemeInterface $theme)
    {
        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/resource')->willReturn(true);

        $this->locateResource('resource', $theme)->shouldReturn('/theme/path/resource');
    }

    public function it_throws_an_exception_if_resource_can_not_be_located(Filesystem $filesystem, ThemeInterface $theme)
    {
        $theme->getName()->willReturn('theme/name');
        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/resource')->willReturn(false);

        $this->shouldThrow(ResourceNotFoundException::class)->during('locateResource', ['resource', $theme]);
    }
}
