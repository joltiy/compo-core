<?php

namespace Compo\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * News
 *
 * @ORM\Table(name="news")
 * @ORM\Entity(repositoryClass="Compo\NewsBundle\Repository\NewsRepository")
 */
class News
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ImageEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BodyEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ViewsEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\PublicationAtEntityTrait;
    use \Compo\SeoBundle\Entity\Traits\SeoEntity;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * @ORM\ManyToMany(targetEntity="Compo\NewsBundle\Entity\NewsTag")
     * @ORM\JoinTable(name="news_tags",
     *      joinColumns={@ORM\JoinColumn(name="news_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     */
    protected $tags;

    /**
     * News constructor.
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tag
     *
     * @param \Compo\NewsBundle\Entity\NewsTag $tag
     *
     * @return News
     */
    public function addTag(\Compo\NewsBundle\Entity\NewsTag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \Compo\NewsBundle\Entity\NewsTag $tag
     */
    public function removeTag(\Compo\NewsBundle\Entity\NewsTag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }
}

