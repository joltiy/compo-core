<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\HelperInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface SettingsHelperInterface extends HelperInterface
{
    /**
     * @param string $schemaAlias
     *
     * @return array
     */
    public function getSettings($schemaAlias);
}
