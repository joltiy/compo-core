<?php

namespace Compo\CoreBundle\Doctrine\ORM;

/**
 * {@inheritdoc}
 */
class EntityRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function getChoices()
    {
        $choices = [];

        $items = $this->findBy([], ['name' => 'ASC']);

        foreach ($items as $item) {
            $choices[$item->getName()] = $item->getId();
        }

        return $choices;
    }

    /**
     * @return array
     */
    public function getChoicesAsValues()
    {
        $choices = [];

        $items = $this->findBy([], ['name' => 'ASC']);

        foreach ($items as $item) {
            $choices[$item->getId()] = $item->getName();
        }

        return $choices;
    }
}
