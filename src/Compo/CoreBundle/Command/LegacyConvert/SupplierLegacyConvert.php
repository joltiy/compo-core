<?php

namespace Compo\CoreBundle\Command\LegacyConvert;


use Compo\SupplierBundle\Entity\Supplier;

/**
 * Class ArticlesLegacyConvert
 * @package Compo\CoreBundle\Command\LegacyConvert
 */
class SupplierLegacyConvert extends BaseLegacyConvert
{
    /**
     *
     */
    public function configure()
    {
        $this->setTableName('suppliers');
        $this->setRepositoryName('CompoSupplierBundle:Supplier');
        $this->setEntityClass(Supplier::class);
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem Supplier
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $newItem->setName($oldDataItem['header']);
        $newItem->setEnabled(true);

        $newItem->setId($oldDataItem['id']);
    }
}