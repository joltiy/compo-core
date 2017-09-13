<?php
/*
 * This file is part of the pixSortableBehaviorBundle.
 *
 * (c) Nicolas Ricci <nicolas.ricci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pix\SortableBehaviorBundle\Services;

use Doctrine\ORM\EntityManagerInterface;

class PositionORMHandler extends PositionHandler
{
    /** @noinspection PhpUndefinedClassInspection */

    /**
     *
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getLastPosition($entity)
    {
        $query = $this->em->createQuery(sprintf(
            'SELECT MAX(m.%s) FROM %s m',
            $positionFiles = $this->getPositionFieldByEntity($entity),
            $entity
        ));
        $result = $query->getResult();

        if (array_key_exists(0, $result)) {
            return (int)$result[0][1];
        }

        return 0;
    }


}
