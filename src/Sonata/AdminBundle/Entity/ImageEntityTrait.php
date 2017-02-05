<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ImageEntityTrait
{
    /**
     * Изображение
     *
     * @ORM\ManyToOne(targetEntity="Compo\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $image;

    /**
     * @return mixed
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