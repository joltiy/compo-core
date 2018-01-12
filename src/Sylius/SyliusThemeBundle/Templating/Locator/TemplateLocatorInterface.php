<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Templating\Locator;

use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface TemplateLocatorInterface
{
    /**
     * @param TemplateReferenceInterface $template
     * @param ThemeInterface             $theme
     *
     * @throws ResourceNotFoundException
     *
     * @return string
     */
    public function locateTemplate(TemplateReferenceInterface $template, ThemeInterface $theme);
}
