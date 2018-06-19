<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\DashboardBundle\Entity;

use Compo\Sonata\AdminBundle\Repository\ChoicesTrait;
use Doctrine\ORM\EntityRepository;

/**
 * Class DashboardRepository.
 */
class DashboardRepository extends EntityRepository
{
    use ChoicesTrait;
}
