<?php

namespace Compo\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ContainerAwareTrait
 *
 * @package Compo\CoreBundle\DependencyInjection
 */
trait ContainerAwareTrait
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}