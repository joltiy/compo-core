<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\HierarchyProvider;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface ThemeHierarchyProviderInterface
{
    /**
     * @param ThemeInterface|null $theme
     *
     * @throws \InvalidArgumentException if dependencies could not be resolved
     *
     * @return ThemeInterface[]
     */
    public function getThemeHierarchy(ThemeInterface $theme = null);
}
