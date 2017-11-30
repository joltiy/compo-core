<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\FaqBundle\Entity\Faq;

/**
 * Class ArticlesLegacyConvert.
 */
class FaqLegacyConvert extends BaseLegacyConvert
{
    public function configure()
    {
        $this->setTableName('faq');
        $this->setRepositoryName('CompoFaqBundle:Faq');
        $this->setEntityClass(Faq::class);
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem Faq
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $newItem->setId($oldDataItem['id']);
        $newItem->setName($oldDataItem['header']);

        $newItem->setAnswer($oldDataItem['text']);

        if ($oldDataItem['bank'] && !$newItem->getImage()) {
            $newItem->setImage($this->downloadMedia($oldDataItem['bank']));
        }

        $newItem->setEnabled((bool) $oldDataItem['visible']);

        $newItem->setUsername($oldDataItem['fname']);
        $newItem->setEmail($oldDataItem['email']);
        $newItem->setSlug($oldDataItem['id']);

        $newItem->setCreatedAt(new \DateTime($oldDataItem['pdate']));
        $newItem->setPublicationAt(new \DateTime($oldDataItem['pdate']));
    }
}
