<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Admin\Traits;

/**
 * Проверка использование трейтов в классах сущности, указанной админки.
 */
trait IsUseEntityTraitsTrait
{
    /**
     * @param \Sonata\AdminBundle\Admin\AdminInterface $admin
     * @param array                                    $traits
     *
     * @return bool
     */
    public function isUseEntityTraits($admin, array $traits = [])
    {
        $traitsAdmin = [];

        $class = $admin->getClass();

        /* @noinspection PhpAssignmentInConditionInspection */
        do {
            /** @noinspection SlowArrayOperationsInLoopInspection */
            $traitsAdmin = array_merge(class_uses($class), $traitsAdmin);
        } while ($class = get_parent_class($class));

        foreach ($traitsAdmin as $trait => $same) {
            /** @noinspection SlowArrayOperationsInLoopInspection */
            $traitsAdmin = array_merge(class_uses($trait), $traitsAdmin);
        }

        $traitsAdmin = array_unique($traitsAdmin);

        foreach ($traits as $traitItem) {
            if (
            !\in_array($traitItem, $traitsAdmin)
            ) {
                return false;
            }
        }

        return true;
    }
}
