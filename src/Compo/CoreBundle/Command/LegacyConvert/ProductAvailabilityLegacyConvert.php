<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\ProductBundle\Entity\ProductAvailability;

/**
 * Class ArticlesLegacyConvert.
 */
class ProductAvailabilityLegacyConvert extends BaseLegacyConvert
{
    public function configure()
    {
        $this->setTableName('ProductAvailability');
        $this->setRepositoryName('CompoProductBundle:ProductAvailability');
        $this->setEntityClass(ProductAvailability::class);
        $this->setHeaderField('name');
    }

    /**
     * @return array
     */
    public function getOldData()
    {
        return [
            [
                'id' => 10,
                'name' => 'В наличии',
                'color' => '',
                'description' => '',
            ],
            [
                'id' => 25,
                'name' => 'Предоплата',
                'color' => '',
                'description' => '',
            ],
            [
                'id' => 20,
                'name' => 'Предзаказ',
                'color' => '',
                'description' => '',
            ],
            [
                'id' => 30,
                'name' => 'Нет в наличии',
                'color' => '',
                'description' => '',
            ],
            [
                'id' => 40,
                'name' => 'Снято с производства',
                'color' => '',
                'description' => '',
            ],
        ];
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem ProductAvailability
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $newItem->setName($oldDataItem['name']);
        $newItem->setId($oldDataItem['id']);
        $newItem->setColor($oldDataItem['color']);
        $newItem->setDescription($oldDataItem['description']);
    }
}
