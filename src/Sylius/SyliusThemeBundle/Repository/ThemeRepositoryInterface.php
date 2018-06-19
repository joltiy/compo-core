<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Repository;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface ThemeRepositoryInterface
{
    /**
     * @return ThemeInterface[]
     */
    public function findAll();

    /**
     * @param string $name
     *
     * @return ThemeInterface|null
     */
    public function findOneByName($name);

    /**
     * @param string $title
     *
     * @return ThemeInterface|null
     */
    public function findOneByTitle($title);
}
