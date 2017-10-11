<?php

namespace Compo\CoreBundle\Command\LegacyConvert;


use Compo\ProductBundle\Entity\Product;

/**
 * Class ArticlesLegacyConvert
 * @package Compo\CoreBundle\Command\LegacyConvert
 */
class ProductLegacyConvert extends BaseLegacyConvert
{

    /**
     * @var array
     */
    public $tovar = array();

    /**
     *
     */
    public function configure()
    {
        $this->setTableName('complects');
        $this->setRepositoryName('CompoProductBundle:Product');
        $this->setEntityClass(Product::class);
    }

    /**
     *
     */
    public function process()
    {
        $tovar = $this->getCommand()->getOldConnection()->fetchAll('SELECT * FROM `tovar`');

        foreach ($tovar as $tovarItem) {
            $this->tovar[$tovarItem['id']] = $tovarItem;
        }

        parent::process();
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem Product
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $catalogRepos = $this->getEntityManager()->getRepository('CompoCatalogBundle:Catalog');

        $newItem->setId($oldDataItem['id']);

        if ($oldDataItem['picture']) {
            $picture = $this->downloadMedia($oldDataItem['picture']);

            if ($picture) {
                $newItem->setImage($picture);
            }
        }

        $newItem->setEnabled((bool)$oldDataItem['visible']);

        $currency_code = $oldDataItem['currency'];

        $currency_code = str_replace(
            array(
                'rur',
                'usd',
                'euro',
            ),
            array(
                'rub',
                'usd',
                'eur',
            ),
            $currency_code
        );

        $currency = $this->getEntityManager()->getRepository('CompoCurrencyBundle:Currency')->findOneBy(array('code' => strtolower($currency_code)));

        $newItem->setCurrency($currency);

        $newItem->setPriceOriginal((int)$oldDataItem['price']);

        if (isset($oldDataItem['price_old'])) {
            $newItem->setPriceOldOriginal((int)$oldDataItem['price_old']);
        }

        $newItem->setSku($oldDataItem['articul']);

        $newItem->setName($oldDataItem['header']);

        $collection = $this->getEntityManager()->getRepository('CompoManufactureBundle:ManufactureCollection')->find($oldDataItem['parent_id']);

        $newItem->setManufactureCollection($collection);

        if (isset($this->tovar[$oldDataItem['parent_id']])) {
            if ((int)$this->tovar[$oldDataItem['parent_id']]['catalog_id'] === 84) {
                $newItem->setCatalog($catalogRepos->find(84));
            } else {
                $newItem->setCatalog($catalogRepos->find(2));
            }
        } else {
            $newItem->setCatalog($catalogRepos->find(2));
        }

        $oldDataItem['suppliers_id'] = null;

        if ($collection) {

            $newItem->setManufacture($collection->getManufacture());

            $oldDataTovar = false;

            if (isset($this->tovar[$oldDataItem['parent_id']])) {
                $oldDataTovar = $this->tovar[$oldDataItem['parent_id']];
            }


            if ($oldDataTovar && !$oldDataItem['suppliers_id']) {
                $oldDataItem['suppliers_id'] = $oldDataTovar['suppliers_id'];
            }
        }


        if ($oldDataItem['novelty']) {
            $newItem->addTag($this->getEntityManager()->getRepository('CompoProductBundle:ProductTag')->find(2));
        }

        if ($oldDataItem['hits']) {
            $newItem->addTag($this->getEntityManager()->getRepository('CompoProductBundle:ProductTag')->find(1));
        }

        if ($oldDataItem['special']) {
            $newItem->addTag($this->getEntityManager()->getRepository('CompoProductBundle:ProductTag')->find(3));
        }


        if ($oldDataItem['suppliers_id']) {
            $sup = $this->getEntityManager()->getRepository('CompoSupplierBundle:Supplier')->find($oldDataItem['suppliers_id']);

            $newItem->addSupplier($sup);
        }


        if ($oldDataItem['state']) {
            $state = $this->getEntityManager()->getRepository('CompoProductBundle:ProductAvailability')->find($oldDataItem['state']);

            $newItem->setAvailability($state);
        }

        $unit_code = $oldDataItem['vtype'];

        $unit_code = str_replace(
            array(
                'шт.',
                'кв.м',
                'компл.',
            ),
            array(
                'Штука',
                'Квадратный метр',
                'Комплект',
            ),
            $unit_code
        );

        $unitCodeEntity = $this->getEntityManager()->getRepository('CompoUnitBundle:Unit')->findOneBy(array('name' => $unit_code));

        $newItem->setUnit($unitCodeEntity);
    }
}
