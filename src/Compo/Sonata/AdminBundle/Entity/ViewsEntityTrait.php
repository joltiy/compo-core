<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait ViewsEntityTrait.
 */
trait ViewsEntityTrait
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    protected $views = 0;

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param int $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }
}
