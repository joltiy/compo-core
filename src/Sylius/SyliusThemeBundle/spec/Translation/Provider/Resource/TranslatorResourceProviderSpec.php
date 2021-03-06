<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Translation\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Resource\TranslatorResourceProvider;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Resource\TranslatorResourceProviderInterface;
use Sylius\Bundle\ThemeBundle\Translation\Resource\TranslationResource;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class TranslatorResourceProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(TranslatorResourceProvider::class);
    }

    public function it_implements_translation_resource_provider_interface()
    {
        $this->shouldImplement(TranslatorResourceProviderInterface::class);
    }

    public function it_transforms_previously_received_paths_into_translation_resources()
    {
        $this->beConstructedWith([
            'messages.en.yml',
            'domain.en.yml',
        ]);

        $this->getResources()->shouldBeLike([
            new TranslationResource('messages.en.yml'),
            new TranslationResource('domain.en.yml'),
        ]);
    }

    public function it_extracts_unique_locales_from_received_paths()
    {
        $this->beConstructedWith([
            'messages.en.yml',
            'domain.en_US.yml',
            'validation.en.yml',
        ]);

        $this->getResourcesLocales()->shouldReturn([
            'en',
            'en_US',
        ]);
    }
}
