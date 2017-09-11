<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait ViewsEntityTrait
 * @package Compo\Sonata\AdminBundle\Entity
 */
trait ViewsEntityTrait
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    protected $views = 0;

    /**
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param integer $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

}