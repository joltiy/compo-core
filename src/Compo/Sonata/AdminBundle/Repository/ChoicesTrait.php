<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Trait ChoicesTrait.
 */
trait ChoicesTrait
{
    /**
     * @return array
     */
    public function getChoices()
    {
        /** @var EntityRepository $repository */
        $repository = $this;

        $choices = [];

        $items = $repository->findBy([], ['name' => 'ASC']);

        foreach ($items as $item) {
            if (method_exists($item, 'getName') && method_exists($item, 'getId')) {
                $choices[$item->getName()] = $item->getId();
            }
        }

        return $choices;
    }

    /**
     * @return array
     */
    public function getChoicesAsValues()
    {
        /** @var EntityRepository $repository */
        $repository = $this;

        $choices = [];

        $items = $repository->findBy([], ['name' => 'ASC']);

        foreach ($items as $item) {
            if (method_exists($item, 'getName') && method_exists($item, 'getId')) {
                $choices[$item->getId()] = $item->getName();
            }
        }

        return $choices;
    }
}
