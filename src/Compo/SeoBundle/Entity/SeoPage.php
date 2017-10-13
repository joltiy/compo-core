<?php

namespace Compo\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)

 * @ORM\Table(name="seo_page")
 * @ORM\Entity(repositoryClass="Compo\SeoBundle\Repository\SeoPageRepository")
 */
class SeoPage
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    protected $header;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    protected $metaKeyword;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    protected $metaDescription;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    protected $descriptionAdditional;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $context;

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMetaKeyword()
    {
        return $this->metaKeyword;
    }

    /**
     * @param string $metaKeyword
     */
    public function setMetaKeyword($metaKeyword)
    {
        $this->metaKeyword = $metaKeyword;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

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

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->context;
    }
}
