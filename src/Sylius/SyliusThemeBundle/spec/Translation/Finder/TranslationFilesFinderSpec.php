<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Translation\Finder;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;
use Sylius\Bundle\ThemeBundle\Translation\Finder\TranslationFilesFinder;
use Sylius\Bundle\ThemeBundle\Translation\Finder\TranslationFilesFinderInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class TranslationFilesFinderSpec extends ObjectBehavior
{
    public function let(FinderFactoryInterface $finderFactory)
    {
        $this->beConstructedWith($finderFactory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TranslationFilesFinder::class);
    }

    public function it_implements_translation_resource_finder_interface()
    {
        $this->shouldImplement(TranslationFilesFinderInterface::class);
    }

    public function it_returns_an_array_of_translation_resources_paths(
        FinderFactoryInterface $finderFactory,
        Finder $finder
    ) {
        $finderFactory->create()->willReturn($finder);

        $finder->in('/theme')->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);

        $finder->getIterator()->willReturn(new \ArrayIterator([
            '/theme/messages.en.yml',
            '/theme/translations/messages.en.yml',
            '/theme/translations/messages.en.yml.jpg',
            '/theme/translations/messages.yml',
            '/theme/AcmeBundle/translations/messages.pl_PL.yml',
        ]));

        $this->findTranslationFiles('/theme')->shouldReturn([
            '/theme/translations/messages.en.yml',
            '/theme/AcmeBundle/translations/messages.pl_PL.yml',
        ]);
    }
}
