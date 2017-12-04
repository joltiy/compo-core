<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\ArticlesBundle\Entity\Articles;

/**
 * Class ArticlesLegacyConvert.
 */
class ArticlesLegacyConvert extends BaseLegacyConvert
{
    public function configure()
    {
        $this->setTableName('articles');
        $this->setRepositoryName('CompoArticlesBundle:Articles');
        $this->setEntityClass(Articles::class);
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem Articles
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $newItem->setId($oldDataItem['id']);
        $newItem->setName($oldDataItem['header']);
        $newItem->setBody($oldDataItem['text']);
        $newItem->setDescription($oldDataItem['daily_block']);

        if ($oldDataItem['bank'] && !$newItem->getImage()) {
            $newItem->setImage($this->getCommand()->downloadMedia($oldDataItem['bank']));
        }

        $newItem->setEnabled((bool) $oldDataItem['visible']);

        $newItem->setPublicationAt(new \DateTime($oldDataItem['pdate']));
        $newItem->setCreatedAt(new \DateTime($oldDataItem['pdate']));

        $newItem->setDescription($oldDataItem['daily_block']);

        $newItem->setSlug(str_replace('.html', '', $oldDataItem['url']));
    }
}
