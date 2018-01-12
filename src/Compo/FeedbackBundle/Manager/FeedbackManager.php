<?php

namespace Compo\FeedbackBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritdoc}
 */
class FeedbackManager extends BaseEntityManager
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    public $types = [];

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
        $choice = [];

        foreach ($this->types as $type_key => $type) {
            $choice[$type_key] = $type_key;
        }

        return $choice;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getType($name)
    {
        return $this->types[$name];
    }
}
