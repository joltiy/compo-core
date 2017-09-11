<?php

namespace Compo\CoreBundle\Doctrine\ORM;

/**
 * {@inheritDoc}
 */
class EntityRepository extends \Doctrine\ORM\EntityRepository
{
    public function getChoices()
    {
        $choices = array();

        $items = $this->findBy(array(), array('name' => 'ASC'));

        foreach ($items as $item) {
            $choices[$item->getName()] = $item->getId();
        }

        return $choices;
    }
}