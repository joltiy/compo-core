<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\NewsBundle\Entity\News;

/**
 * Class ArticlesLegacyConvert
 * @package Compo\CoreBundle\Command\LegacyConvert
 */
class NewsLegacyConvert extends BaseLegacyConvert
{
    /**
     *
     */
    public function configure() {
        $this->setTableName('news');
        $this->setRepositoryName('CompoNewsBundle:News');
        $this->setEntityClass(News::class);
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem News
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem) {
        $newItem->setId($oldDataItem['id']);
        $newItem->setName($oldDataItem['header']);

        $newItem->setBody($oldDataItem['text']);

        if ($oldDataItem['bank'] && !$newItem->getImage()) {
            $newItem->setImage($this->downloadMedia($oldDataItem['bank']));
        }

        $newItem->setEnabled((bool)1);
        $newItem->setDescription($oldDataItem['daily_block']);

        $newItem->setCreatedAt(new \DateTime($oldDataItem['pdate']));
        $newItem->setPublicationAt(new \DateTime($oldDataItem['pdate']));

        $newItem->setSlug(str_replace('.html', '', $oldDataItem['url']));
    }
}