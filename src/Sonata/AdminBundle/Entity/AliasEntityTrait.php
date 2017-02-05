<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait AliasEntityTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $alias;

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
     * @return self
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }
}