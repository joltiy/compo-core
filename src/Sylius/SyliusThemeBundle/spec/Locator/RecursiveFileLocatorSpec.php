<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
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
        $this->beConstructedWith($finderFactory, array('/search/path/'));
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

        $finder->getIterator()->willReturn(new \ArrayIterator(array(
            $splFileInfo->getWrappedObject(),
        )));

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

        $finder->getIterator()->willReturn(new \ArrayIterator(array(
            $firstSplFileInfo->getWrappedObject(),
            $secondSplFileInfo->getWrappedObject(),
        )));

        $firstSplFileInfo->getPathname()->willReturn('/search/path/nested1/readme.md');
        $secondSplFileInfo->getPathname()->willReturn('/search/path/nested2/readme.md');

        $this->locateFilesNamed('readme.md')->shouldReturn(array(
            '/search/path/nested1/readme.md',
            '/search/path/nested2/readme.md',
        ));
    }

    public function it_throws_an_exception_if_searching_for_file_with_empty_name()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFileNamed', array(''));
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFileNamed', array(null));
    }

    public function it_throws_an_exception_if_searching_for_files_with_empty_name()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFilesNamed', array(''));
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFilesNamed', array(null));
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

        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFileNamed', array('readme.md'));
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

        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFilesNamed', array('readme.md'));
    }

    public function it_isolates_finding_paths_from_multiple_sources(
        FinderFactoryInterface $finderFactory,
        Finder $firstFinder,
        Finder $secondFinder,
        SplFileInfo $splFileInfo
    ) {
        $this->beConstructedWith($finderFactory, array('/search/path/first/', '/search/path/second/'));

        $finderFactory->create()->willReturn($firstFinder, $secondFinder);

        $firstFinder->name('readme.md')->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->in('/search/path/first/')->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->files()->shouldBeCalled()->willReturn($firstFinder);

        $secondFinder->name('readme.md')->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->in('/search/path/second/')->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->files()->shouldBeCalled()->willReturn($secondFinder);

        $firstFinder->getIterator()->willReturn(new \ArrayIterator(array($splFileInfo->getWrappedObject())));
        $secondFinder->getIterator()->willReturn(new \ArrayIterator());

        $splFileInfo->getPathname()->willReturn('/search/path/first/nested/readme.md');

        $this->locateFilesNamed('readme.md')->shouldReturn(array(
            '/search/path/first/nested/readme.md',
        ));
    }

    public function it_silences_finder_exceptions_even_if_searching_in_multiple_sources(
        FinderFactoryInterface $finderFactory,
        Finder $firstFinder,
        Finder $secondFinder,
        SplFileInfo $splFileInfo
    ) {
        $this->beConstructedWith($finderFactory, array('/search/path/first/', '/search/path/second/'));

        $finderFactory->create()->willReturn($firstFinder, $secondFinder);

        $firstFinder->name('readme.md')->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->in('/search/path/first/')->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($firstFinder);
        $firstFinder->files()->shouldBeCalled()->willReturn($firstFinder);

        $secondFinder->name('readme.md')->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->in('/search/path/second/')->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($secondFinder);
        $secondFinder->files()->shouldBeCalled()->willReturn($secondFinder);

        $firstFinder->getIterator()->willReturn(new \ArrayIterator(array($splFileInfo->getWrappedObject())));
        $secondFinder->getIterator()->willThrow(\InvalidArgumentException::class);

        $splFileInfo->getPathname()->willReturn('/search/path/first/nested/readme.md');

        $this->locateFilesNamed('readme.md')->shouldReturn(array(
            '/search/path/first/nested/readme.md',
        ));
    }
}
