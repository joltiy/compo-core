<?php

namespace Compo\SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Social
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Compo\SocialBundle\Entity\SocialRepository")
 */
class Social
{
    use \Compo\Sonata\AdminBundle\Entity\PositionEntityTrait;
    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ImageEntityTrait;

    /**
     * Описание
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $url;

    /**
     * Описание
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $icon;


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Social constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }


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
     * Set id
     *
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }



}
