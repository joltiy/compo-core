<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\CountryBundle\Entity\Country;

/**
 * Class ArticlesLegacyConvert.
 */
class CountryLegacyConvert extends BaseLegacyConvert
{
    public function configure()
    {
        $this->setTableName('countrys');
        $this->setRepositoryName('CompoCountryBundle:Country');
        $this->setEntityClass(Country::class);
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem Country
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $newItem->setName($oldDataItem['header']);
        $newItem->setEnabled((bool) $oldDataItem['visible']);
        $newItem->setSlug(str_replace('.html', '', $oldDataItem['alt_name']));
        $newItem->setId($oldDataItem['id']);
        $newItem->setCode(substr($oldDataItem['alt_name'], 0, 2));

        $newItem->setHeader($oldDataItem['ht_h1']);
        $newItem->setDescription($oldDataItem['ht_text'] . '<!--more-->' . $oldDataItem['ht_text_bottom']);
        $newItem->setMetaDescription($oldDataItem['ht_desc']);
        $newItem->setMetaKeyword($oldDataItem['ht_words']);
    }
}
