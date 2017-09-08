<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Compo\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Mapping as ORM;

trait ImageEntityTrait
{
    /**
     * Изображение
     *
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Compo\Sonata\MediaBundle\Entity\Media",  fetch="EXTRA_LAZY")
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