<?php

namespace Compo\MenuBundle\Entity;

use Compo\TaggingBundle\Entity\Tagging;
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
    use \Compo\Sonata\AdminBundle\Entity\TreeEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;
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
     * @ORM\ManyToOne(targetEntity="Compo\CountryBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $country;
    /**
     * @var integer
     *
     * @ORM\Column(name="country_id", type="integer", nullable=true)
     */
    protected $country_id;
    /**
     * @ORM\ManyToOne(targetEntity="Compo\ManufactureBundle\Entity\Manufacture")
     * @ORM\JoinColumn(name="manufacture_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $manufacture;
    /**
     * @var integer
     *
     * @ORM\Column(name="manufacture_id", type="integer", nullable=true)
     */
    protected $manufacture_id;

    /**
     * @ORM\ManyToOne(targetEntity="Compo\TaggingBundle\Entity\Tagging")
     * @ORM\JoinColumn(name="tagging_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $tagging;

    /**
     * @ORM\ManyToOne(targetEntity="Compo\CatalogBundle\Entity\Catalog")
     * @ORM\JoinColumn(name="catalog_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $catalog;

    /**
     * @var integer
     *
     * @ORM\Column(name="tagging_id", type="integer", nullable=true)
     */
    protected $tagging_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="page_id", type="integer", nullable=true)
     */
    protected $page_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="catalog_id", type="integer", nullable=true)
     */
    protected $catalog_id;

    /**
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $target;

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
     * @return mixed
     */
    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * @param mixed $catalog
     */
    public function setCatalog($catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * @return int
     */
    public function getCatalogId()
    {
        return $this->catalog_id;
    }

    /**
     * @param int $catalog_id
     */
    public function setCatalogId($catalog_id)
    {
        $this->catalog_id = $catalog_id;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getManufacture()
    {
        return $this->manufacture;
    }

    /**
     * @param mixed $manufacture
     */
    public function setManufacture($manufacture)
    {
        $this->manufacture = $manufacture;
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
     * @return Tagging
     */
    public function getTagging()
    {
        return $this->tagging;
    }

    /**
     * @param mixed $tagging
     */
    public function setTagging($tagging)
    {
        $this->tagging = $tagging;
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
     * @return mixed
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * @param mixed $country_id
     */
    public function setCountryId($country_id)
    {
        $this->country_id = $country_id;
    }

    /**
     * @return mixed
     */
    public function getManufactureId()
    {
        return $this->manufacture_id;
    }

    /**
     * @param mixed $manufacture_id
     */
    public function setManufactureId($manufacture_id)
    {
        $this->manufacture_id = $manufacture_id;
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
