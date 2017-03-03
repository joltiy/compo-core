<?php

namespace Compo\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * MenuItem
 *
 * @Gedmo\Tree(type="nested")
 * @Gedmo\Loggable
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
 *          @ORM\Index(name="lvl_deleted_at", columns={"lvl", "deleted_at" }),
 *          @ORM\Index(name="alias", columns={"alias" }),
 *          @ORM\Index(name="alias_deleted_at", columns={"alias", "deleted_at" })
 *     }
 * )
 * @ORM\Entity(repositoryClass="Compo\MenuBundle\Entity\MenuItemRepository")
 */
class MenuItem
{
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\TreeEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $alias;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * @return MenuItem
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
     * @return MenuItem
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return MenuItem
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

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
