<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Admin\Traits;

/**
 * Добавление HTML аттрибута class для таблицы списка элементов.
 */
trait DatagridTableClassTrait
{
    /**
     * @var array
     */
    public $datagridAttributes = [];

    /**
     * @var string
     */
    public $datagridTableClass = '';

    /**
     * @return string
     */
    public function getDatagridTableClass()
    {
        return $this->datagridTableClass;
    }

    /**
     * @param string $datagridTableClass
     */
    public function setDatagridTableClass($datagridTableClass)
    {
        $this->datagridTableClass = $datagridTableClass;
    }

    /**
     * @return array
     */
    public function getDatagridAttributes(): array
    {
        return $this->datagridAttributes;
    }

    /**
     * @param array $datagridAttributes
     */
    public function setDatagridAttributes(array $datagridAttributes): void
    {
        $this->datagridAttributes = $datagridAttributes;
    }

    /**
     * @param $name
     * @param $value
     */
    public function addDatagridAttribute($name, $value): void
    {
        $this->datagridAttributes[$name] = $value;
    }
}
