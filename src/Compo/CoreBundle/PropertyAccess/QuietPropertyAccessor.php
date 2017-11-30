<?php

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
