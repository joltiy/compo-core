<?php

namespace Compo\CoreBundle\Command\LegacyConvert;


use Compo\ManufactureBundle\Entity\Manufacture;

/**
 * Class ArticlesLegacyConvert
 * @package Compo\CoreBundle\Command\LegacyConvert
 */
class ManufactureLegacyConvert extends BaseLegacyConvert
{
    /**
     *
     */
    public function configure()
    {
        $this->setTableName('manufacture');
        $this->setRepositoryName('CompoManufactureBundle:Manufacture');
        $this->setEntityClass(Manufacture::class);
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem Manufacture
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $countryRepository = $this->getEntityManager()->getRepository('CompoCountryBundle:Country');

        $newItem->setName($oldDataItem['header']);

        $newItem->setEnabled((bool)$oldDataItem['visible']);


        $description = '';

        if (isset($oldDataItem['mini'])) {
            $description .= $oldDataItem['mini'];
        }

        $newItem->setDescription($description);

        $newItem->setSlug(str_replace('.html', '', $oldDataItem['url']));
        $newItem->setId($oldDataItem['id']);


        if ($oldDataItem['picture'] && !$newItem->getImage()) {
            $newItem->setImage($this->downloadMedia($oldDataItem['picture']));
        }


        if ($oldDataItem['country']) {
            $country = $countryRepository->find($oldDataItem['country']);

            if ($country) {
                $newItem->setCountry($country);
            }
        }
    }
}
