<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Transformer;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ParameterTransformerInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function transform($value);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function reverseTransform($value);
}
