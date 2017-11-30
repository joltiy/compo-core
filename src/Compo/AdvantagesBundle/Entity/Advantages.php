<?php

namespace Compo\AdvantagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Advantages.
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(name="deleted_at", columns={"deleted_at" }),
 *          @ORM\Index(name="id_deleted_at", columns={"id", "deleted_at" })
 *     }
 * )
 * @ORM\Entity(repositoryClass="Compo\AdvantagesBundle\Entity\AdvantagesRepository")
 */
class Advantages
{
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\DescriptionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;

    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Gedmo\Timestampable\Traits\TimestampableEntity;

    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * @ORM\OneToMany(targetEntity="Compo\AdvantagesBundle\Entity\AdvantagesItem", mappedBy="advantages", cascade={"all"}))
     */
    protected $items;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add item.
     *
     * @param \Compo\AdvantagesBundle\Entity\AdvantagesItem $item
     *
     * @return self
     */
    public function addItem(\Compo\AdvantagesBundle\Entity\AdvantagesItem $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item.
     *
     * @param \Compo\AdvantagesBundle\Entity\AdvantagesItem $item
     */
    public function removeItem(\Compo\AdvantagesBundle\Entity\AdvantagesItem $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Get items.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }
}
