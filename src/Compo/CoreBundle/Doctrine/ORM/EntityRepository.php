<?php

namespace Compo\CoreBundle\Doctrine\ORM;

class EntityRepository extends \Doctrine\ORM\EntityRepository
{
    public function getChoices()
    {
        $choices = array();

        $items = $this->findBy(array(), array('name' => 'ASC'));

        foreach ($items as $item) {
            $choices[$item->getId()] = $item->getName();
        }

        return $choices;
    }
}