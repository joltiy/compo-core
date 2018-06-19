<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Repository;

use Doctrine\ORM\EntityManager;

/**
 * Trait ViewsRepositoryTrait.
 */
trait ViewsTrait
{
    /**
     * @param $object \Compo\Sonata\AdminBundle\Entity\ViewsEntityTrait
     */
    public function increaseViews($object)
    {
        //$views = $object->getViews() + 1;

        //$object->setViews($views);

        /* @var EntityManager $em */
        //$em = $this->getEntityManager();

        //$em->persist($object);

        //$em->flush();
    }
}
