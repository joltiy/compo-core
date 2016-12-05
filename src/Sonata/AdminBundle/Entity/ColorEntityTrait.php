<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

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

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }


    public function getColorHtml() {
        return '<i class="fa fa-square" style="color: ' . $this->getColor(). ';"></i>';
    }
}