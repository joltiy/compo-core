<?php

namespace Compo\ArticlesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Articles
 *
 * @ORM\Table(name="articles")
 * @ORM\Entity(repositoryClass="Compo\ArticlesBundle\Repository\ArticlesRepository")
 */
class Articles
{
    use \Compo\Sonata\AdminBundle\Entity\PositionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    use \Compo\SeoBundle\Entity\Traits\SeoEntity;

    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ImageEntityTrait;


    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $publicationAt;


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function __construct()
    {
        $this->publicationAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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

