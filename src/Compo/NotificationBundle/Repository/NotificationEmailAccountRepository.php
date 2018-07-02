<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\NotificationBundle\Repository;

use Compo\Sonata\AdminBundle\Repository\ChoicesTrait;
use Doctrine\ORM\EntityRepository;

/**
 * NotificationEmailAccountRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotificationEmailAccountRepository extends EntityRepository
{
    use ChoicesTrait;

    public function getDefaultId()
    {
        $currency = $this->findOneBy([], ['id' => 'ASC']);

        if ($currency) {
            return $currency->getId();
        }

        return null;
    }
}
