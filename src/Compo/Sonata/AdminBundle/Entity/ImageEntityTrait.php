<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Compo\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait ImageEntityTrait
 * @package Compo\Sonata\AdminBundle\Entity
 */
trait ImageEntityTrait
{
    /**
     * Изображение
     *
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Compo\Sonata\MediaBundle\Entity\Media",  fetch="EAGER")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $image;

    /**
     * @return Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
}
