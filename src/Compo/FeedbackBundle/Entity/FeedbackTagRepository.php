<?php

namespace Compo\FeedbackBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeedbackTagRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getChoices()
    {
        $choices = array();

        /** @var FeedbackTag[] $items */
        $items = $this->findBy(array(), array('name' => 'ASC'));


        foreach ($items as $item) {
            $choices[$item->getId()] = $item->getName();
        }

        return $choices;
    }
}