<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;

/**
 * {@inheritdoc}
 */
class ThemeManager implements ThemeContextInterface
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    public $themeName = 'default';

    /**
     * @return \Sylius\Bundle\ThemeBundle\Model\ThemeInterface|null
     */
    public function getTheme()
    {
        return $this->getContainer()->get('sylius.repository.theme')->findOneByName($this->themeName);
    }

    /**
     * @param string $theme
     */
    public function setThemeName($theme)
    {
        $this->themeName = $theme;
    }
}
