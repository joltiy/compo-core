<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait FileEntityTrait
 * @package Compo\Sonata\AdminBundle\Entity
 */
trait FileEntityTrait
{
    /**
     * Изображение
     *
     * @ORM\ManyToOne(targetEntity="Compo\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $file;

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }
}