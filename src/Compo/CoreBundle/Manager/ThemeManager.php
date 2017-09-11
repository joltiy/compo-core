<?php

namespace Compo\CoreBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;

/**
 * {@inheritDoc}
 */
class ThemeManager implements ThemeContextInterface
{
    use ContainerAwareTrait;

    public $themeName = 'default';

    /**
     * @return null|\Sylius\Bundle\ThemeBundle\Model\ThemeInterface
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