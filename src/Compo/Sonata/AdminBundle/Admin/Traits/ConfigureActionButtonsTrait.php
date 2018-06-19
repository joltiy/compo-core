<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Admin\Traits;

/**
 * ConfigureActionButtons.
 */
trait ConfigureActionButtonsTrait
{
    /**
     * По умолчанию для всех элементов - нет действий.
     *
     * Правый верхний угол: http://prntscr.com/j2rybf
     *
     * @param string $action
     * @param null   $object
     *
     * @return array
     */
    public function configureActionButtons($action, $object = null)
    {
        return [];
    }
}
