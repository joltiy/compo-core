<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\FeedbackBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

/**
 * Class FeedbackManager
 */
class FeedbackManager
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
