<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\FeaturesBundle\Entity\FeatureAttribute;
use Compo\FeaturesBundle\Entity\FeatureValue;
use Compo\FeaturesBundle\Entity\FeatureVariant;

/**
 * Class ArticlesLegacyConvert.
 */
class FeaturesValueLegacyConvert extends BaseLegacyConvert
{
    public function configure()
    {
        $this->setTableName('feature_value_complects');
        $this->setRepositoryName('CompoFeaturesBundle:FeatureValue');
        $this->setEntityClass(FeatureValue::class);
        $this->setHeaderField('id');
        $this->setBatchSize(50000);
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem FeatureValue
     *
     * @return bool
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $featureValue = $newItem;
        $oldProductFeaturesItem = $oldDataItem;

        $product = $this->getEntityManager()->getRepository('CompoProductBundle:Product')->find($oldDataItem['entity_id']);

        if (!$product) {
            return false;
        }

        $featureValue->setId($oldProductFeaturesItem['id']);

        /** @var FeatureAttribute $featureAttribute */
        $featureAttribute = $this->getEntityManager()->getRepository('CompoFeaturesBundle:FeatureAttribute')->find($oldProductFeaturesItem['feature_attribute_id']);

        if ('variant' === $featureAttribute->getType()) {
            if ($oldProductFeaturesItem['value_variant_id']) {
                /** @var FeatureVariant $featureVariant */
                $featureVariant = $this->getEntityManager()->getRepository('CompoFeaturesBundle:FeatureVariant')->find($oldProductFeaturesItem['value_variant_id']);

                if ($featureVariant) {
                    $featureValue->setValueVariant($featureVariant);
                    $featureValue->setFeature($featureAttribute);
                    $featureValue->setProduct($product);
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } elseif ('integer' === $featureAttribute->getType()) {
            $featureValue->setValueInteger($oldProductFeaturesItem['value_integer']);

            $featureValue->setFeature($featureAttribute);
            $featureValue->setProduct($product);
        } elseif ('decimal' === $featureAttribute->getType()) {
            $featureValue->setValueDecimal($oldProductFeaturesItem['value_decimal']);

            $featureValue->setFeature($featureAttribute);
            $featureValue->setProduct($product);
        } elseif ('string' === $featureAttribute->getType()) {
            $featureValue->setValueDecimal($oldProductFeaturesItem['value_string']);

            $featureValue->setFeature($featureAttribute);
            $featureValue->setProduct($product);
        } else {
            return false;
        }
    }
}
