<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait AliasEntityTrait.
 */
trait AliasEntityTrait
{
    /**
     * URL.
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    protected $alias;

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set alias.
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
