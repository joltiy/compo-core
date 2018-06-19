<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Locator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;
use Sylius\Bundle\ThemeBundle\Locator\RecursiveFileLocator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class RecursiveFileLocatorSpec extends ObjectBehavior
{
    public function let(FinderFactoryInterface $finderFactory)
    {
        $this->beConstructedWith($finderFactory, ['/search/path/']);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RecursiveFileLocator::class);
    }

    public function it_implements_sylius_file_locator_interface()
    {
        $this->shouldImplement(FileLocatorInterface::class);
    }

    public function it_searches_for_file(FinderFactoryInterface $finderFactory, Finder $finder, SplFileInfo $splFileInfo)
    {
        $finderFactory->create()->willReturn($finder);

        $finder->name('readme.md')->shouldBeCalled()->willReturn($finder);
        $finder->in('/search/path/')->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);
        $finder->files()->shouldBeCalled()->willReturn($finder);

        $finder->getIterator()->willReturn(new \ArrayIterator([
            $splFileInfo->getWrappedObject(),
        ]));

        $splFileInfo->getPathname()->willReturn('/search/path/nested/readme.md');

        $this->locateFileNamed('readme.md')->shouldReturn('/search/path/nested/readme.md');
    }

    public function it_searches_for_files(
        FinderFactoryInterface $finderFactory,
        Finder $finder,
        SplFileInfo $firstSplFileInfo,
        SplFileInfo $secondSplFileInfo
    ) {
        $finderFactory->create()->willReturn($finder);

        $finder->name('readme.md')->shouldBeCalled()->willReturn($finder);
        $finder->in('/search/path/')->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);
        $finder->files()->shouldBeCalled()->willReturn($finder);

        $finder->getIterator()->willReturn(new \ArrayIterator([
            $firstSplFileInfo->getWrappedObject(),
            $secondSplFileInfo->getWrappedObject(),
        ]));

        $firstSplFileInfo->getPathname()->willReturn('/search/path/nested1/readme.md');
        $secondSplFileInfo->getPathname()->willReturn('/search/path/nested2/readme.md');

        $this->locateFilesNamed('readme.md')->shouldReturn([
            '/search/path/nested1/readme.md',
            '/search/path/nested2/readme.md',
        ]);
    }

    public function it_throws_an_exception_if_searching_for_file_with_empty_name()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFileNamed', ['']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFileNamed', [null]);
    }

    public function it_throws_an_exception_if_searching_for_files_with_empty_name()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFilesNamed', ['']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFilesNamed', [null]);
    }

    public function it_throws_an_exception_if_there_is_no_file_that_matches_the_given_name(
        FinderFactoryInterface $finderFactory,
        Finder $finder
    ) {
        $finderFactory->create()->willReturn($finder);

        $finder->name('readme.md')->shouldBeCalled()->willReturn($finder);
        $finder->in('/search/path/')->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);
        $finder->files()->shouldBeCalled()->willReturn($finder);

        $finder->getIterator()->willReturn(new \ArrayIterator());

        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFileNamed', ['readme.md']);
    }

    public function it_throws_an_exception_if_there_is_there_are_not_any_files_that_matches_the_given_name(
        FinderFactoryInterface $finderFactory,
        Finder $finder
    ) {
        $finderFactory->create()->willReturn($finder);

        $finder->name('readme.md')->shouldBeCalled()->willReturn($finder);
        $finder->in('/search/path/')->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);
        $finder->files()->shouldBeCalled()->willReturn($finder);

        $finder->getIterator()->willReturn(new \ArrayIterator());

        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFilesNamed', ['readme.md']);
    }

    public function it_isolates_finding_paths_from_multiple_sources(
        FinderFactoryInterface $finderFactory,
        Finder $firstFinder,
        Finder $secondFinder,
        SplFileInfo $splFileInfo
    ) {
        $this->beConstructedWith($finderFactory, ['/search/path/first/', '/search/path/second/']);

        $finderFactory->create()->willReturn($firstFinder, $secondFinder);

        $firstFinder->name('readme.md')->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->in('/search/path/first/')->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->files()->shouldBeCalled()->willReturn($firstFinder);

        $secondFinder->name('readme.md')->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->in('/search/path/second/')->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->files()->shouldBeCalled()->willReturn($secondFinder);

        $firstFinder->getIterator()->willReturn(new \ArrayIterator([$splFileInfo->getWrappedObject()]));
        $secondFinder->getIterator()->willReturn(new \ArrayIterator());

        $splFileInfo->getPathname()->willReturn('/search/path/first/nested/readme.md');

        $this->locateFilesNamed('readme.md')->shouldReturn([
            '/search/path/first/nested/readme.md',
        ]);
    }

    public function it_silences_finder_exceptions_even_if_searching_in_multiple_sources(
        FinderFactoryInterface $finderFactory,
        Finder $firstFinder,
        Finder $secondFinder,
        SplFileInfo $splFileInfo
    ) {
        $this->beConstructedWith($finderFactory, ['/search/path/first/', '/search/path/second/']);

        $finderFactory->create()->willReturn($firstFinder, $secondFinder);

        $firstFinder->name('readme.md')->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->in('/search/path/first/')->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->files()->shouldBeCalled()->willReturn($firstFinder);

        $secondFinder->name('readme.md')->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->in('/search/path/second/')->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->files()->shouldBeCalled()->willReturn($secondFinder);

        $firstFinder->getIterator()->willReturn(new \ArrayIterator([$splFileInfo->getWrappedObject()]));
        $secondFinder->getIterator()->willThrow(\InvalidArgumentException::class);

        $splFileInfo->getPathname()->willReturn('/search/path/first/nested/readme.md');

        $this->locateFilesNamed('readme.md')->shouldReturn([
            '/search/path/first/nested/readme.md',
        ]);
    }
}
