<?php

namespace Compo\RedirectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Redirect
 *
 * @ORM\Table(name="redirect")
 * @ORM\Entity(repositoryClass="Compo\RedirectBundle\Repository\RedirectRepository")
 */
class Redirect
{
    use \Compo\Sonata\AdminBundle\Entity\PositionEntityTrait;
    use \Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * URL
     *
     * @var string
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    protected $urIn;

    /**
     * URL
     *
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $urOut;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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


    public function __toString()
    {
        return $this->urIn;
    }
}
