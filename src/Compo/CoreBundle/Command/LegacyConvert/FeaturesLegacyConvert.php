<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\FeaturesBundle\Entity\FeatureAttribute;
use Compo\FeaturesBundle\Entity\FeatureVariant;

/**
 * Class ArticlesLegacyConvert.
 */
class FeaturesLegacyConvert extends BaseLegacyConvert
{
    /**
     * @var array
     */
    public $feature_catalog = [];

    public function configure()
    {
        $this->setTableName('feature_attribute');
        $this->setRepositoryName('CompoFeaturesBundle:FeatureAttribute');
        $this->setEntityClass(FeatureAttribute::class);
    }

    public function process()
    {
        $featureAttributeRepositoru = $this->getEntityManager()->getRepository('CompoFeaturesBundle:FeatureAttribute');
        $featureVariantRepositoru = $this->getEntityManager()->getRepository('CompoFeaturesBundle:FeatureVariant');

        $this->getCommand()->clearCurrent($featureAttributeRepositoru);
        $this->getCommand()->clearCurrent($featureVariantRepositoru);
        $this->feature_catalog = $this->getCommand()->getOldConnection()->fetchAll('SELECT * FROM `feature_catalog` ORDER BY id');

        parent::process();
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem FeatureAttribute
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $catalogRepos = $this->getEntityManager()->getRepository('CompoCatalogBundle:Catalog');
        $unitRepos = $this->getEntityManager()->getRepository('CompoUnitBundle:Unit');
        $featureVariantRepositoru = $this->getEntityManager()->getRepository('CompoFeaturesBundle:FeatureVariant');

        $newItem->setId($oldDataItem['id']);

        if ('decimal' === $oldDataItem['value_type']) {
            $newItem->setType('decimal');
        } elseif ('integer' === $oldDataItem['value_type']) {
            $newItem->setType('integer');
        } elseif ('variant' === $oldDataItem['value_type']) {
            $newItem->setType('variant');
        } elseif ('string' === $oldDataItem['value_type']) {
            $newItem->setType('string');
        }

        if (false !== mb_strpos($oldDataItem['header'], 'Размер дверцы')) {
            $newItem->setType('decimal');
        }

        $newItem->setVisibleFilter(1);
        $newItem->setVisibleCard(1);
        $newItem->setVisibleCollection(1);
        $newItem->setEnabled(1);

        $name = $oldDataItem['header'];

        $name = str_replace(['(см)', '(кг)', '(мм)'], '', $name);

        $newItem->setName(trim($name));

        foreach ($this->feature_catalog as $feature_catalog_item) {
            if ((int) $feature_catalog_item['feature_attribute_id'] === (int) $oldDataItem['id']) {
                if (84 === (int) $feature_catalog_item['catalog_id']) {
                    $newItem->setCatalog($catalogRepos->find(84));
                } else {
                    $newItem->setCatalog($catalogRepos->find(2));
                }
            } else {
                if (84 !== (int) $feature_catalog_item['catalog_id']) {
                    $newItem->setCatalog($catalogRepos->find(2));
                }
            }
        }

        if (false !== mb_strpos($oldDataItem['header'], '(см)')) {
            $newItem->setUnit($unitRepos->findOneBy(['name' => 'Сантиметр']));
        }

        if (false !== mb_strpos($oldDataItem['header'], '(кг)')) {
            $newItem->setUnit($unitRepos->findOneBy(['name' => 'Килограмм']));
        }

        if (false !== mb_strpos($oldDataItem['header'], '(мм)')) {
            $newItem->setUnit($unitRepos->findOneBy(['name' => 'Миллиметр']));
        }

        if (false !== mb_strpos($oldDataItem['header'], 'Размер чипа')) {
            $newItem->setUnit($unitRepos->findOneBy(['name' => 'Миллиметр']));
        }

        if (false !== mb_strpos($oldDataItem['header'], 'Штук в упаковке')) {
            $newItem->setUnit($unitRepos->findOneBy(['name' => 'Штука']));
        }

        if ('variant' === $oldDataItem['value_type']) {
            $feature_values = $this->getCommand()->getOldConnection()->fetchAll('SELECT * FROM `feature_variant` WHERE feature_attribute_id=' . $oldDataItem['id']);

            foreach ($feature_values as $feature_values_item) {
                $fv = $featureVariantRepositoru->find($feature_values_item['id']);

                if (!$fv) {
                    $fv = new FeatureVariant();
                }

                if ('' === trim($fv->getDescription()) && '' !== trim(strip_tags($feature_values_item['description']))) {
                    $fv->setDescription($feature_values_item['description']);
                }

                $this->getCommand()->changeIdGenerator($fv);

                $fv->setId($feature_values_item['id']);

                $fv->setFeature($newItem);

                $fv->setFeature($newItem);
                $fv->setEnabled(1);

                $fv->setName($feature_values_item['header']);

                $this->getEntityManager()->persist($fv);
            }
        }
    }
}
