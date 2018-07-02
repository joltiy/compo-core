<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\MenuBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\MenuBundle\Menu\MenuItemType;

/**
 * Class MenuManager
 * @package Compo\MenuBundle\Manager
 */
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

    /**
     * @param MenuItemType $menuType
     */
    public function addMenuType($menuType): void
    {
        $this->menuTypes[$menuType->getName()] = $menuType;
    }

    /**
     * @return array
     */
    public function getMenuTypeChoices()
    {
        $choices = [];

        foreach ($this->menuTypes as $menuType) {
            $name = $menuType->getName();

            $choices['menu_type.' . $name] = $name;
        }

        return $choices;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getMenuType($name)
    {
        return $this->menuTypes[$name];
    }
}
