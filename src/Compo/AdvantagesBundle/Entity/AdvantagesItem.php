<?php

namespace Compo\AdvantagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AdvantagesItem
 *
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(name="enabled", columns={"enabled" }),
 *          @ORM\Index(name="deleted_at", columns={"deleted_at" }),
 *          @ORM\Index(name="enabled_deleted_at", columns={"enabled", "deleted_at" }),
 *          @ORM\Index(name="id_deleted_at", columns={"id", "deleted_at" })
 *     }
 * )
 * @ORM\Entity(repositoryClass="Compo\AdvantagesBundle\Entity\AdvantagesItemRepository")
 */
class AdvantagesItem
{
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\PositionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ImageEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;


    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url;


    /**
     * @ORM\ManyToOne(targetEntity="Compo\AdvantagesBundle\Entity\Advantages", fetch="EAGER")
     * @ORM\JoinColumn(name="advantages_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $advantages;


    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="Compo\Sonata\PageBundle\Entity\Page")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $page;

    /**
     * @var integer
     *
     * @ORM\Column(name="page_id", type="integer", nullable=true)
     */
    protected $page_id;


    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }


    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return AdvantagesItem
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return AdvantagesItem
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getAdvantages()
    {
        return $this->advantages;
    }

    /**
     * @param mixed $advantages
     */
    public function setAdvantages($advantages)
    {
        $this->advantages = $advantages;
    }


}
