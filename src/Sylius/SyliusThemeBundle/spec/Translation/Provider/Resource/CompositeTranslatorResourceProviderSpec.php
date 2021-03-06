<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Translation\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Resource\CompositeTranslatorResourceProvider;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Resource\TranslatorResourceProviderInterface;
use Sylius\Bundle\ThemeBundle\Translation\Resource\TranslationResourceInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class CompositeTranslatorResourceProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(CompositeTranslatorResourceProvider::class);
    }

    public function it_implements_translator_resource_provider_interface()
    {
        $this->shouldImplement(TranslatorResourceProviderInterface::class);
    }

    public function it_aggregates_the_resources(
        TranslatorResourceProviderInterface $firstResourceProvider,
        TranslatorResourceProviderInterface $secondResourceProvider,
        TranslationResourceInterface $firstResource,
        TranslationResourceInterface $secondResource
    ) {
        $this->beConstructedWith([$firstResourceProvider, $secondResourceProvider]);

        $firstResourceProvider->getResources()->willReturn([$firstResource]);
        $secondResourceProvider->getResources()->willReturn([$secondResource, $firstResource]);

        $this->getResources()->shouldReturn([$firstResource, $secondResource, $firstResource]);
    }

    public function it_aggregates_the_resources_locales(
        TranslatorResourceProviderInterface $firstResourceProvider,
        TranslatorResourceProviderInterface $secondResourceProvider
    ) {
        $this->beConstructedWith([$firstResourceProvider, $secondResourceProvider]);

        $firstResourceProvider->getResourcesLocales()->willReturn(['first-locale']);
        $secondResourceProvider->getResourcesLocales()->willReturn(['second-locale']);

        $this->getResourcesLocales()->shouldReturn(['first-locale', 'second-locale']);
    }

    public function it_aggregates_the_unique_resources_locales(
        TranslatorResourceProviderInterface $firstResourceProvider,
        TranslatorResourceProviderInterface $secondResourceProvider
    ) {
        $this->beConstructedWith([$firstResourceProvider, $secondResourceProvider]);

        $firstResourceProvider->getResourcesLocales()->willReturn(['first-locale']);
        $secondResourceProvider->getResourcesLocales()->willReturn(['second-locale', 'first-locale', 'second-locale']);

        $this->getResourcesLocales()->shouldReturn(['first-locale', 'second-locale']);
    }
}
