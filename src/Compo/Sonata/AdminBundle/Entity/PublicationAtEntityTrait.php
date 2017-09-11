<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait PublicationAtEntityTrait
 * @package Compo\Sonata\AdminBundle\Entity
 */
trait PublicationAtEntityTrait
{

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $publicationAt;

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getPublicationAt()
    {
        return $this->publicationAt;
    }

    /**
     * Sets createdAt.
     *
     * @param  \DateTime $createdAt
     * @return $this
     */
    public function setPublicationAt(\DateTime $createdAt)
    {
        $this->publicationAt = $createdAt;

        return $this;
    }
}