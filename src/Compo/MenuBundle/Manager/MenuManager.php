<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\MenuBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

class MenuManager
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    public $menuTypes = [];

    /**
     * @return array
     */
    public function getMenuTypes(): array
    {
        return $this->menuTypes;
    }

    /**
     * @param array $menuTypes
     */
    public function setMenuTypes(array $menuTypes): void
    {
        $this->menuTypes = $menuTypes;
    }

    public function addMenuType($menuType): void
    {
        $this->menuTypes[$menuType->getName()] = $menuType;
    }

    public function getMenuTypeChoices()
    {
        $choices = [];

        foreach ($this->menuTypes as $menuType) {
            $choices['menu_type.' . $menuType->getName()] = $menuType->getName();
        }

        return $choices;
    }

    public function getMenuType($name)
    {
        return $this->menuTypes[$name];
    }
}
