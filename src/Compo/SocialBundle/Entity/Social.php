<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Social.
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(name="deleted_at", columns={"deleted_at" }),
 *          @ORM\Index(name="position", columns={"position" }),
 *          @ORM\Index(name="deleted_at_position", columns={ "deleted_at", "position" })
 *     }
 * )
 * @ORM\Entity(repositoryClass="Compo\SocialBundle\Entity\SocialRepository")
 */
class Social
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\PositionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\NameEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\ImageEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;

    use \Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * Описание.
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $url;

    /**
     * Описание.
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $icon;

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
}
