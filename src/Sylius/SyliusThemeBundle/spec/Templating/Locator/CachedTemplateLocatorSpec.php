<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Templating\Locator;

use Doctrine\Common\Cache\Cache;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Templating\Locator\CachedTemplateLocator;
use Sylius\Bundle\ThemeBundle\Templating\Locator\TemplateLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class CachedTemplateLocatorSpec extends ObjectBehavior
{
    public function let(TemplateLocatorInterface $decoratedTemplateLocator, Cache $cache)
    {
        $this->beConstructedWith($decoratedTemplateLocator, $cache);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CachedTemplateLocator::class);
    }

    public function it_implements_template_locator_interface()
    {
        $this->shouldImplement(TemplateLocatorInterface::class);
    }

    public function it_returns_the_location_found_in_cache(
        TemplateLocatorInterface $decoratedTemplateLocator,
        Cache $cache,
        TemplateReferenceInterface $template,
        ThemeInterface $theme
    ) {
        $template->getLogicalName()->willReturn('Logical:Name');
        $theme->getName()->willReturn('theme/name');

        $cache->contains('Logical:Name|theme/name')->willReturn(true);
        $cache->fetch('Logical:Name|theme/name')->willReturn('/template.html.twig');

        $decoratedTemplateLocator->locateTemplate(Argument::cetera())->shouldNotBeCalled();

        $this->locateTemplate($template, $theme)->shouldReturn('/template.html.twig');
    }

    public function it_uses_decorated_template_locator_if_location_can_not_be_found_in_cache(
        TemplateLocatorInterface $decoratedTemplateLocator,
        Cache $cache,
        TemplateReferenceInterface $template,
        ThemeInterface $theme
    ) {
        $template->getLogicalName()->willReturn('Logical:Name');
        $theme->getName()->willReturn('theme/name');

        $cache->contains('Logical:Name|theme/name')->willReturn(false);
        $cache->fetch(Argument::cetera())->shouldNotBeCalled();

        $decoratedTemplateLocator->locateTemplate($template, $theme)->willReturn('/template.html.twig');

        $this->locateTemplate($template, $theme)->shouldReturn('/template.html.twig');
    }

    public function it_throws_resource_not_found_exception_if_the_location_found_in_cache_is_null(
        TemplateLocatorInterface $decoratedTemplateLocator,
        Cache $cache,
        TemplateReferenceInterface $template,
        ThemeInterface $theme
    ) {
        $template->getLogicalName()->willReturn('Logical:Name');
        $template->getPath()->willReturn('@Acme/template.html.twig');
        $theme->getName()->willReturn('theme/name');

        $cache->contains('Logical:Name|theme/name')->willReturn(true);
        $cache->fetch('Logical:Name|theme/name')->willReturn(null);

        $decoratedTemplateLocator->locateTemplate(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(ResourceNotFoundException::class)->during('locateTemplate', [$template, $theme]);
    }
}
