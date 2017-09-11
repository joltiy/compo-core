<?php

namespace Compo\CoreBundle\Doctrine\ORM;

/**
 * {@inheritDoc}
 */
class EntityRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function getChoices()
    {
        $choices = array();

        $items = $this->findBy(array(), array('name' => 'ASC'));

        foreach ($items as $item) {
            /** @noinspection PhpUndefinedMethodInspection */
            $choices[$item->getName()] = $item->getId();
        }

        return $choices;
    }
}