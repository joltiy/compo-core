<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\PropertyAccess;

use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * {@inheritdoc}
 */
class QuietPropertyAccessor extends PropertyAccessor
{
    /**
     * {@inheritdoc}
     */
    public function getValue($objectOrArray, $propertyPath)
    {
        try {
            return parent::getValue($objectOrArray, $propertyPath);
        } catch (\Exception $e) {
            return null;
        }
    }
}
