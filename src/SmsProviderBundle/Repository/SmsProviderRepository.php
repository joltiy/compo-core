<?php

namespace Compo\SmsProviderBundle\Repository;

use Compo\SmsProviderBundle\Entity\SmsProvider;

/**
 * SmsProviderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SmsProviderRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function getSmsProviderChoices()
    {
        $choices = array();

        /** @var SmsProvider[] $items */
        $items = $this->findBy(array(), array('name' => 'ASC'));

        foreach ($items as $item) {
            $choices[$item->getId()] = $item->getName();
        }

        return $choices;
    }
}
