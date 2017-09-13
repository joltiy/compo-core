<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\EntityManager;


/**
 * Trait ViewsRepositoryTrait
 * @package Compo\Sonata\AdminBundle\Entity
 */
trait ViewsRepositoryTrait
{
    /**
     * @param $object \Compo\Sonata\AdminBundle\Entity\ViewsEntityTrait
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function increaseViews($object)
    {
        $views = $object->getViews() + 1;

        $object->setViews($views);

        /** @var EntityManager $em */
        $em = $this->getEntityManager();

        $em->persist($object);

        $em->flush();
    }
}