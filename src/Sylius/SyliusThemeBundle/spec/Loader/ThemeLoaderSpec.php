<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Factory\ThemeAuthorFactoryInterface;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Factory\ThemeScreenshotFactoryInterface;
use Sylius\Bundle\ThemeBundle\Loader\CircularDependencyCheckerInterface;
use Sylius\Bundle\ThemeBundle\Loader\CircularDependencyFoundException;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoader;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoaderInterface;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoadingFailedException;
use Sylius\Bundle\ThemeBundle\Model\ThemeAuthor;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeScreenshot;
use Zend\Hydrator\HydrationInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ThemeLoaderSpec extends ObjectBehavior
{
    public function let(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        ThemeAuthorFactoryInterface $themeAuthorFactory,
        ThemeScreenshotFactoryInterface $themeScreenshotFactory,
        HydrationInterface $themeHydrator,
        CircularDependencyCheckerInterface $circularDependencyChecker
    ) {
        $this->beConstructedWith(
            $configurationProvider,
            $themeFactory,
            $themeAuthorFactory,
            $themeScreenshotFactory,
            $themeHydrator,
            $circularDependencyChecker
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ThemeLoader::class);
    }

    public function it_implements_theme_loader_interface()
    {
        $this->shouldImplement(ThemeLoaderInterface::class);
    }

    public function it_loads_a_single_theme(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        HydrationInterface $themeHydrator,
        CircularDependencyCheckerInterface $circularDependencyChecker,
        ThemeInterface $theme
    ) {
        $configurationProvider->getConfigurations()->willReturn(array(
            array(
                'name' => 'first/theme',
                'path' => '/theme/path',
                'parents' => array(),
                'authors' => array(),
                'screenshots' => array(),
            ),
        ));

        $themeFactory->create('first/theme', '/theme/path')->willReturn($theme);

        $themeHydrator->hydrate(array(
            'name' => 'first/theme',
            'path' => '/theme/path',
            'parents' => array(),
            'authors' => array(),
            'screenshots' => array(),
        ), $theme)->willReturn($theme);

        $circularDependencyChecker->check($theme)->shouldBeCalled();

        $this->load()->shouldReturn(array($theme));
    }

    public function it_loads_a_theme_with_author(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        ThemeAuthorFactoryInterface $themeAuthorFactory,
        HydrationInterface $themeHydrator,
        CircularDependencyCheckerInterface $circularDependencyChecker,
        ThemeInterface $theme
    ) {
        $themeAuthor = new ThemeAuthor();

        $configurationProvider->getConfigurations()->willReturn(array(
            array(
                'name' => 'first/theme',
                'path' => '/theme/path',
                'parents' => array(),
                'authors' => array(array('name' => 'Richard Rynkowsky')),
                'screenshots' => array(),
            ),
        ));

        $themeFactory->create('first/theme', '/theme/path')->willReturn($theme);
        $themeAuthorFactory->createFromArray(array('name' => 'Richard Rynkowsky'))->willReturn($themeAuthor);

        $themeHydrator->hydrate(array(
            'name' => 'first/theme',
            'path' => '/theme/path',
            'parents' => array(),
            'authors' => array($themeAuthor),
            'screenshots' => array(),
        ), $theme)->willReturn($theme);

        $circularDependencyChecker->check($theme)->shouldBeCalled();

        $this->load()->shouldReturn(array($theme));
    }

    public function it_loads_a_theme_with_screenshot(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        ThemeScreenshotFactoryInterface $themeScreenshotFactory,
        HydrationInterface $themeHydrator,
        CircularDependencyCheckerInterface $circularDependencyChecker,
        ThemeInterface $theme
    ) {
        $themeScreenshot = new ThemeScreenshot('screenshot/omg.jpg');

        $configurationProvider->getConfigurations()->willReturn(array(
            array(
                'name' => 'first/theme',
                'path' => '/theme/path',
                'parents' => array(),
                'authors' => array(),
                'screenshots' => array(
                    array('path' => 'screenshot/omg.jpg', 'title' => 'Title'),
                ),
            ),
        ));

        $themeFactory->create('first/theme', '/theme/path')->willReturn($theme);
        $themeScreenshotFactory->createFromArray(array('path' => 'screenshot/omg.jpg', 'title' => 'Title'))->willReturn($themeScreenshot);

        $themeHydrator->hydrate(array(
            'name' => 'first/theme',
            'path' => '/theme/path',
            'parents' => array(),
            'authors' => array(),
            'screenshots' => array($themeScreenshot),
        ), $theme)->willReturn($theme);

        $circularDependencyChecker->check($theme)->shouldBeCalled();

        $this->load()->shouldReturn(array($theme));
    }

    public function it_loads_a_theme_with_its_dependency(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        HydrationInterface $themeHydrator,
        CircularDependencyCheckerInterface $circularDependencyChecker,
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme
    ) {
        $configurationProvider->getConfigurations()->willReturn(array(
            array(
                'name' => 'first/theme',
                'path' => '/first/theme/path',
                'parents' => array('second/theme'),
                'authors' => array(),
                'screenshots' => array(),
            ),
            array(
                'name' => 'second/theme',
                'path' => '/second/theme/path',
                'parents' => array(),
                'authors' => array(),
                'screenshots' => array(),
            ),
        ));

        $themeFactory->create('first/theme', '/first/theme/path')->willReturn($firstTheme);
        $themeFactory->create('second/theme', '/second/theme/path')->willReturn($secondTheme);

        $themeHydrator->hydrate(array(
            'name' => 'first/theme',
            'path' => '/first/theme/path',
            'parents' => array($secondTheme),
            'authors' => array(),
            'screenshots' => array(),
        ), $firstTheme)->willReturn($firstTheme);
        $themeHydrator->hydrate(array(
            'name' => 'second/theme',
            'path' => '/second/theme/path',
            'parents' => array(),
            'authors' => array(),
            'screenshots' => array(),
        ), $secondTheme)->willReturn($secondTheme);

        $circularDependencyChecker->check($firstTheme)->shouldBeCalled();
        $circularDependencyChecker->check($secondTheme)->shouldBeCalled();

        $this->load()->shouldReturn(array($firstTheme, $secondTheme));
    }

    public function it_throws_an_exception_if_requires_not_existing_dependency(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        ThemeInterface $firstTheme
    ) {
        $configurationProvider->getConfigurations()->willReturn(array(
            array(
                'name' => 'first/theme',
                'path' => '/theme/path',
                'parents' => array('second/theme'),
                'authors' => array(),
                'screenshots' => array(),
            ),
        ));

        $themeFactory->create('first/theme', '/theme/path')->willReturn($firstTheme);

        $this
            ->shouldThrow(new ThemeLoadingFailedException('Unexisting theme "second/theme" is required by "first/theme".'))
            ->during('load')
        ;
    }

    public function it_throws_an_exception_if_there_is_a_circular_dependency_found(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        HydrationInterface $themeHydrator,
        CircularDependencyCheckerInterface $circularDependencyChecker,
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme
    ) {
        $configurationProvider->getConfigurations()->willReturn(array(
            array(
                'name' => 'first/theme',
                'path' => '/first/theme/path',
                'parents' => array('second/theme'),
                'authors' => array(),
                'screenshots' => array(),
            ),
            array(
                'name' => 'second/theme',
                'path' => '/second/theme/path',
                'parents' => array('first/theme'),
                'authors' => array(),
                'screenshots' => array(),
            ),
        ));

        $themeFactory->create('first/theme', '/first/theme/path')->willReturn($firstTheme);
        $themeFactory->create('second/theme', '/second/theme/path')->willReturn($secondTheme);

        $themeHydrator->hydrate(array(
            'name' => 'first/theme',
            'path' => '/first/theme/path',
            'parents' => array($secondTheme),
            'authors' => array(),
            'screenshots' => array(),
        ), $firstTheme)->willReturn($firstTheme);
        $themeHydrator->hydrate(array(
            'name' => 'second/theme',
            'path' => '/second/theme/path',
            'parents' => array($firstTheme),
            'authors' => array(),
            'screenshots' => array(),
        ), $secondTheme)->willReturn($secondTheme);

        $circularDependencyChecker->check(Argument::cetera())->willThrow(CircularDependencyFoundException::class);

        $this
            ->shouldThrow(new ThemeLoadingFailedException('Circular dependency found.'))
            ->during('load')
        ;
    }
}
