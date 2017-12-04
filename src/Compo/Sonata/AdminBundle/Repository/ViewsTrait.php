<?php

namespace Compo\Sonata\AdminBundle\Repository;

use Doctrine\ORM\EntityManager;

/**
 * Trait ViewsRepositoryTrait.
 */
trait ViewsTrait
{
    /**
     * @param $object \Compo\Sonata\AdminBundle\Entity\ViewsEntityTrait
     *
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
