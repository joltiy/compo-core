<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\BannerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Banner.
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(name="deleted_at", columns={"deleted_at" }),
 *          @ORM\Index(name="id_deleted_at", columns={"id", "deleted_at" })
 *     }
 * )
 * @ORM\Entity(repositoryClass="Compo\BannerBundle\Entity\BannerRepository")
 */
class Banner
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait;

    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $options;

    /**
     * @ORM\OrderBy({"position" = "ASC"})
     * @ORM\OneToMany(targetEntity="Compo\BannerBundle\Entity\BannerItem", mappedBy="banner", cascade={"all"}))
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
     * @param BannerItem $item
     *
     * @return self
     */
    public function addItem(BannerItem $item)
    {
        $item->setBanner($this);

        $this->getItems()->add($item);

        return $this;
    }

    /**
     * Remove item.
     *
     * @param BannerItem $item
     */
    public function removeItem(BannerItem $item)
    {
        $item->setBanner(null);

        $this->getItems()->removeElement($item);
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

    /**
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
}
