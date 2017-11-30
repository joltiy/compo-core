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
use Sylius\Bundle\ThemeBundle\Loader\CircularDependencyChecker;
use Sylius\Bundle\ThemeBundle\Loader\CircularDependencyCheckerInterface;
use Sylius\Bundle\ThemeBundle\Loader\CircularDependencyFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class CircularDependencyCheckerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(CircularDependencyChecker::class);
    }

    public function it_implements_circular_dependency_checker_interface()
    {
        $this->shouldImplement(CircularDependencyCheckerInterface::class);
    }

    public function it_does_not_find_circular_dependency_if_checking_a_theme_without_any_parents(
        ThemeInterface $theme
    ) {
        $theme->getParents()->willReturn(array());

        $this->check($theme);
    }

    public function it_does_not_find_circular_dependency_if_theme_parents_are_not_cycled(
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme,
        ThemeInterface $thirdTheme,
        ThemeInterface $fourthTheme
    ) {
        $firstTheme->getParents()->willReturn(array($secondTheme, $thirdTheme));
        $secondTheme->getParents()->willReturn(array($thirdTheme, $fourthTheme));
        $thirdTheme->getParents()->willReturn(array($fourthTheme));
        $fourthTheme->getParents()->willReturn(array());

        $this->check($firstTheme);
    }

    public function it_finds_circular_dependency_if_theme_parents_are_cycled(
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme,
        ThemeInterface $thirdTheme,
        ThemeInterface $fourthTheme
    ) {
        $firstTheme->getParents()->willReturn(array($secondTheme, $thirdTheme));
        $secondTheme->getParents()->willReturn(array($thirdTheme));
        $thirdTheme->getParents()->willReturn(array($fourthTheme));
        $fourthTheme->getParents()->willReturn(array($secondTheme));

        $firstTheme->getName()->willReturn('first/theme');
        $secondTheme->getName()->willReturn('second/theme');
        $thirdTheme->getName()->willReturn('third/theme');
        $fourthTheme->getName()->willReturn('fourth/theme');

        $this
            ->shouldThrow(CircularDependencyFoundException::class)
            ->during('check', array($firstTheme))
        ;
    }
}
