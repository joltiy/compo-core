<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait ColorEntityTrait
 * @package Compo\Sonata\AdminBundle\Entity
 */
trait ColorEntityTrait
{
    /**
     * Цвет
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $color;

    /**
     * @return string
     */
    public function getColorHtml()
    {
        return '<i class="fa fa-square" style="color: ' . $this->getColor() . ';"></i>';
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return self
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }
}