<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


trait DescriptionAdditionalEntityTrait
{
    /**
     * Описание
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $descriptionAdditional;

    /**
     * @return string
     */
    public function getDescriptionAdditional()
    {
        return $this->descriptionAdditional;
    }

    /**
     * @param string $descriptionAdditional
     */
    public function setDescriptionAdditional($descriptionAdditional)
    {
        $this->descriptionAdditional = $descriptionAdditional;
    }

}