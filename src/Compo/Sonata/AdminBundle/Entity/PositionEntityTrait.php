<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait PositionEntityTrait.
 */
trait PositionEntityTrait
{
    /**
     * @Gedmo\SortablePosition()
     * @ORM\Column(name="position", type="integer")
     */
    protected $position = -1;

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}
