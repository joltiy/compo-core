<?php

namespace Compo\CoreBundle\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * {@inheritDoc}
 */
class QuietPropertyAccessor extends PropertyAccessor
{

    /**
     * {@inheritDoc}
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