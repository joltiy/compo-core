<?php

namespace Compo\FeedbackBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritDoc}
 */
class FeedbackManager extends BaseEntityManager
{
    use ContainerAwareTrait;

    public $types = array();

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param array $types
     */
    public function setTypes($types)
    {
        foreach ($types as $type_key => $type) {
            $this->types[$type['type']] = $type;
        }
    }

    /**
     * @return array
     */
    public function getTypesChoice()
    {
        $choice = array();

        foreach ($this->types as $type_key => $type) {
            $choice[$type_key] = $type_key;
        }

        return $choice;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getType($name)
    {
        return $this->types[$name];
    }
}