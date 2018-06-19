<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Templating\Locator;

use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class TemplateLocator implements TemplateLocatorInterface
{
    /**
     * @var ResourceLocatorInterface
     */
    private $resourceLocator;

    /**
     * @param ResourceLocatorInterface $resourceLocator
     */
    public function __construct(ResourceLocatorInterface $resourceLocator)
    {
        $this->resourceLocator = $resourceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function locateTemplate(TemplateReferenceInterface $template, ThemeInterface $theme)
    {
        return $this->resourceLocator->locateResource($template->getPath(), $theme);
    }
}
