<?php

namespace Compo\CoreBundle\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class QuietPropertyAccessor extends PropertyAccessor
{

    public function getValue($objectOrArray, $propertyPath)
    {

        try {
            return parent::getValue($objectOrArray, $propertyPath);
        } catch (\Exception $e) {
            return null;
        }
    }
}