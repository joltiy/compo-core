<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\ProductBundle\Entity\ProductTag;

/**
 * Class ArticlesLegacyConvert.
 */
class ProductTagLegacyConvert extends BaseLegacyConvert
{
    public function configure()
    {
        $this->setTableName('tags');
        $this->setRepositoryName('CompoProductBundle:ProductTag');
        $this->setEntityClass(ProductTag::class);
        $this->setHeaderField('name');
    }

    /**
     * @return array
     */
    public function getOldData()
    {
        return array(
            array(
                'id' => 1,
                'name' => 'Хит',
            ),
            array(
                'id' => 2,
                'name' => 'Новинка',
            ),
            array(
                'id' => 3,
                'name' => 'Акция',
            ),
        );
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem ProductTag
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $newItem->setId($oldDataItem['id']);
        $newItem->setName($oldDataItem['name']);
        $newItem->setEnabled(true);
    }
}
