<?php

namespace Compo\Sonata\AdminBundle\Entity;

use Doctrine\ORM\QueryBuilder;

trait ViewsRepositoryTrait
{
   public function increaseViews($object)
   {
       $views = $object->getViews() + 1;

       $object->setViews($views);

       $em = $this->getEntityManager();

       $em->persist($object);

       $em->flush();
   }
}