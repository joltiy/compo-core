<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\MenuBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * MenuItem.
 *
 * @Gedmo\Tree(type="nested")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(name="enabled", columns={"enabled" }),
 *          @ORM\Index(name="deleted_at", columns={"deleted_at" }),
 *          @ORM\Index(name="enabled_deleted_at", columns={"enabled", "deleted_at" }),
 *          @ORM\Index(name="rgt", columns={"rgt" }),
 *          @ORM\Index(name="lft", columns={"lft" }),
 *          @ORM\Index(name="root", columns={"root" }),
 *          @ORM\Index(name="root_lft", columns={"root", "lft" }),
 *          @ORM\Index(name="id_deleted_at", columns={"id", "deleted_at" }),
 *          @ORM\Index(name="lvl", columns={"lvl" }),
 *          @ORM\Index(name="lvl_deleted_at", columns={"lvl", "deleted_at" })
 *     }
 * )
 * @ORM\Entity(repositoryClass="Compo\MenuBundle\Entity\MenuItemRepository")
 */
class MenuItem
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\TreeNestedEntityTrait;

    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ImageEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait;

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
     * @ORM\ManyToOne(targetEntity="Compo\MenuBundle\Entity\Menu", fetch="EAGER")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $menu;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Compo\MenuBundle\Entity\MenuItem", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Compo\MenuBundle\Entity\MenuItem", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Compo\MenuBundle\Entity\MenuItem")
     * @ORM\Column(type="integer")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    protected $root;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    protected $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    protected $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    protected $rgt;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $targetId;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $target;

    /**
     * @return mixed
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * @param mixed $targetId
     */
    public function setTargetId($targetId): void
    {
        $this->targetId = $targetId;
    }

    /**
     * Get lft.
     *
     * @return int
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lft.
     *
     * @param int $lft
     *
     * @return self
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lvl.
     *
     * @return int
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set lvl.
     *
     * @param int $lvl
     *
     * @return self
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get rgt.
     *
     * @return int
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set rgt.
     *
     * @param int $rgt
     *
     * @return self
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return MenuItem
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent.
     *
     * @param MenuItem $parent
     *
     * @return MenuItem
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Add children.
     *
     * @param MenuItem $children
     *
     * @return MenuItem
     */
    public function addChild($children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children.
     *
     * @param MenuItem $children
     */
    public function removeChild($children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param mixed $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

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
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return MenuItem
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return MenuItem
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param mixed $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }
}
