<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Loader;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class CircularDependencyChecker implements CircularDependencyCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function check(ThemeInterface $theme, array $previousThemes = [])
    {
        if (0 === \count($theme->getParents())) {
            return;
        }

        $previousThemes = array_merge($previousThemes, [$theme]);
        foreach ($theme->getParents() as $parent) {
            if (\in_array($parent, $previousThemes, true)) {
                throw new CircularDependencyFoundException(array_merge($previousThemes, [$parent]));
            }

            $this->check($parent, $previousThemes);
        }
    }
}
