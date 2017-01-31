<?php
/**
 * Created by PhpStorm.
 * User: jivoy1988
 * Date: 28.03.16
 * Time: 15:21
 */


namespace Compo\SeoBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SeoEntity
{

    /**
     * URL
     *
     * @var string
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    protected $slug;

    /**
     * Title
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * Meta Keywords
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaKeyword;

    /**
     * Meta Descriptions
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metaDescription;


    /**
     * Заголовок на странице
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $header;


    /**
     * Запрет индексирование
     *
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    protected $noIndexEnabled = false;

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
     * @return boolean
     */
    public function isNoIndexEnabled()
    {
        return $this->noIndexEnabled;
    }

    /**
     * Get noIndexEnabled
     *
     * @return boolean
     */
    public function getNoIndexEnabled()
    {
        return $this->noIndexEnabled;
    }

    /**
     * @param boolean $noIndexEnabled
     */
    public function setNoIndexEnabled($noIndexEnabled)
    {
        $this->noIndexEnabled = $noIndexEnabled;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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


}