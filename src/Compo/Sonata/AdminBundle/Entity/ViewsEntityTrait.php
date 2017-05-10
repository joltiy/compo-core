<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ViewsEntityTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    protected $views = 0;

    /**
     * @return string
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param string $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

}