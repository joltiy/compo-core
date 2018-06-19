<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Form\Factory;

use Symfony\Component\Form\FormInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SettingsFormFactoryInterface
{
    /**
     * @param string     $schemaAlias
     * @param mixed|null $data
     * @param array      $options
     *
     * @return FormInterface
     */
    public function create($schemaAlias, $data = null, array $options = []);
}
