<?php

namespace Compo\RedirectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Redirect.
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(
 *     name="redirect",
 *     indexes={
 *          @ORM\Index(name="idx_enabled", columns={"enabled"}),
 *          @ORM\Index(name="idx_deleted_at", columns={"deleted_at"}),
 *          @ORM\Index(name="idx_enabled_deleted_at", columns={"enabled", "deleted_at"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="Compo\RedirectBundle\Repository\RedirectRepository")
 */
class Redirect
{
    use \Compo\Sonata\AdminBundle\Entity\IdEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait;

    use \Gedmo\Timestampable\Traits\TimestampableEntity;
    use \Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

    /**
     * URL.
     *
     * @var string
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    protected $urIn;

    /**
     * URL.
     *
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $urOut;

    /**
     * @return string
     */
    public function getUrIn()
    {
        return $this->urIn;
    }

    /**
     * @param string $urIn
     */
    public function setUrIn($urIn)
    {
        $this->urIn = $urIn;
    }

    /**
     * @return string
     */
    public function getUrOut()
    {
        return $this->urOut;
    }

    /**
     * @param string $urOut
     */
    public function setUrOut($urOut)
    {
        $this->urOut = $urOut;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->urIn;
    }
}
