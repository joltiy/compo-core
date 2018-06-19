<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * News.
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @ORM\Table(name="news",
 *     indexes={
 *
 *          @ORM\Index(name="publication_at_enabled_deleted_at", columns={"publication_at","enabled","deleted_at" }),

 *          @ORM\Index(name="publication_at", columns={"publication_at" }),
 *          @ORM\Index(name="enabled", columns={"enabled" }),
 *          @ORM\Index(name="deleted_at", columns={"deleted_at" }),
 *
 *          @ORM\Index(name="enabled_deleted_at", columns={"enabled", "deleted_at" })
 *     }
 * )
 * @ORM\Entity(repositoryClass="Compo\NewsBundle\Repository\NewsRepository")
 */
class News
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ImageEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ViewsEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\PublicationAtEntityTrait;
    use \Compo\SeoBundle\Entity\Traits\SeoEntity;
    use \Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait;

    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * @ORM\ManyToMany(targetEntity="Compo\NewsBundle\Entity\NewsTag", indexBy="id")
     * @ORM\JoinTable(name="news_tags",
     *      joinColumns={@ORM\JoinColumn(name="news_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     */
    protected $tags;

    /**
     * Текст
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * News constructor.
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tag.
     *
     * @param \Compo\NewsBundle\Entity\NewsTag $tag
     *
     * @return News
     */
    public function addTag(NewsTag $tag)
    {
        $this->tags[$tag->getId()] = $tag;

        return $this;
    }

    /**
     * Remove tag.
     *
     * @param \Compo\NewsBundle\Entity\NewsTag $tag
     */
    public function removeTag(NewsTag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        $removeTags = [];

        foreach ($tags as $tagKey => $tag) {
            foreach ($this->getTags() as $tagCurrent) {
                if ($tagCurrent === $tag) {
                    unset($tags[$tagKey]);
                    continue;
                }
                $removeTags[] = $tagCurrent;
            }
        }

        foreach ($tags as $tag) {
            $this->addTag($tag);
        }

        foreach ($removeTags as $tag) {
            $this->removeTag($tag);
        }
    }

    /**
     * @return string
     */
    public function getTagsExportAsString()
    {
        $tags = [];

        foreach ($this->getTags() as $tag) {
            $tags[] = $tag->getName();
        }

        return implode(', ', $tags);
    }
}
