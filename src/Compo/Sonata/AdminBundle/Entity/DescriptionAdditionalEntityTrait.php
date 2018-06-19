<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Дополнительное описание.
 */
trait DescriptionAdditionalEntityTrait
{
    /**
     * Дополнительное описание.
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $descriptionAdditional;

    /**
     * @return string
     */
    public function getDescriptionAdditional()
    {
        return $this->descriptionAdditional;
    }

    /**
     * @param string $descriptionAdditional
     *
     * @return self
     */
    public function setDescriptionAdditional($descriptionAdditional)
    {
        $this->descriptionAdditional = $descriptionAdditional;

        return $this;
    }
}
