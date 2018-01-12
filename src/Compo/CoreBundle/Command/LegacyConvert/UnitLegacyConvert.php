<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\UnitBundle\Entity\Unit;

/**
 * Class ArticlesLegacyConvert.
 */
class UnitLegacyConvert extends BaseLegacyConvert
{
    public function configure()
    {
        $this->setTableName('unit');
        $this->setRepositoryName('CompoUnitBundle:Unit');
        $this->setEntityClass(Unit::class);
        $this->setHeaderField('name');
    }

    /**
     * @return array
     */
    public function getOldData()
    {
        return [
            [
                'id' => 1,
                'name' => 'Штука',
                'shortNameNational' => 'шт',
                'literalNameNational' => 'ШТ',
                'shortNameInternational' => 'pc',
                'literalNameInternational' => 'PCE',
            ],
            [
                'id' => 2,
                'name' => 'Комплект',
                'shortNameNational' => 'компл',
                'literalNameNational' => 'КОМПЛ',
                'shortNameInternational' => 'set',
                'literalNameInternational' => 'SET',
            ],
            [
                'id' => 3,
                'name' => 'Квадратный метр',
                'shortNameNational' => 'м2',
                'literalNameNational' => 'М2',
                'shortNameInternational' => 'm2',
                'literalNameInternational' => 'MTK',
            ],
            [
                'id' => 4,
                'name' => 'Сантиметр',
                'shortNameNational' => 'см',
                'literalNameNational' => 'СМ',
                'shortNameInternational' => 'cm',
                'literalNameInternational' => 'CMT',
            ],
            [
                'id' => 5,
                'name' => 'Метр',
                'shortNameNational' => 'м',
                'literalNameNational' => 'М',
                'shortNameInternational' => 'm',
                'literalNameInternational' => 'MTR',
            ],
            [
                'id' => 6,
                'name' => 'Миллиметр',
                'shortNameNational' => 'мм',
                'literalNameNational' => 'ММ',
                'shortNameInternational' => 'mm',
                'literalNameInternational' => 'MMT',
            ],
            [
                'id' => 7,
                'name' => 'Килограмм',
                'shortNameNational' => 'кг',
                'literalNameNational' => 'КГ',
                'shortNameInternational' => 'kg',
                'literalNameInternational' => 'KGM',
            ],
        ];
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem Unit
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $newItem->setId($oldDataItem['id']);
        $newItem->setName($oldDataItem['name']);
        $newItem->setShortNameNational($oldDataItem['shortNameNational']);
        $newItem->setLiteralNameNational($oldDataItem['literalNameNational']);
        $newItem->setShortNameInternational($oldDataItem['shortNameInternational']);
        $newItem->setLiteralNameInternational($oldDataItem['literalNameInternational']);
    }
}
