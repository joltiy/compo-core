<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\CurrencyBundle\Entity\Currency;

/**
 * Class ArticlesLegacyConvert.
 */
class CurrencyLegacyConvert extends BaseLegacyConvert
{
    public function configure()
    {
        $this->setTableName('currency');
        $this->setRepositoryName('CompoCurrencyBundle:Currency');
        $this->setEntityClass(Currency::class);
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
                'name' => 'Рубль',
                'description' => 'Российский рубль',
                'symbol' => 'р.',
                'sign' => '&#8381;',
                'code' => 'RUB',
                'rate' => '1',
            ],
            [
                'id' => 2,
                'name' => 'Доллар',
                'description' => 'Доллар США',
                'symbol' => '$',
                'sign' => '$',
                'code' => 'USD',
                'rate' => '66',
            ],
            [
                'id' => 3,
                'name' => 'Евро',
                'description' => 'Евро',
                'symbol' => 'евро',
                'sign' => '&#8364;',
                'code' => 'EUR',
                'rate' => '77',
            ],
        ];
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem Currency
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $newItem->setName($oldDataItem['name']);
        $newItem->setId($oldDataItem['id']);
        $newItem->setCode($oldDataItem['code']);
        //$newItem->setDescription($oldDataItem['description']);
        $newItem->setSymbol($oldDataItem['symbol']);
        $newItem->setSign($oldDataItem['sign']);
        $newItem->setRate($oldDataItem['rate']);
    }
}
