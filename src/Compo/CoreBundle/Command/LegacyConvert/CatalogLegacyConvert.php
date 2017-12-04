<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

use Compo\CatalogBundle\Entity\Catalog;

/**
 * Class ArticlesLegacyConvert.
 */
class CatalogLegacyConvert extends BaseLegacyConvert
{
    public function configure()
    {
        $this->setTableName('catalog');
        $this->setRepositoryName('CompoCatalogBundle:Catalog');
        $this->setEntityClass(Catalog::class);
    }

    /**
     * @return array
     */
    public function getOldData()
    {
        return array(
            1 => array(
                'id' => 1,
                'header' => 'Каталог',
                'parent' => null,
            ),
            2 => array(
                'id' => 2,
                'header' => 'Плитка',
                'parent' => 1,
            ),
            84 => array(
                'id' => 84,
                'header' => 'Люки',
                'parent' => 1,
            ),
        );
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem Catalog
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $currentRepository = $this->getCommand()->getCurrentRepository($this->getRepositoryName());
        $newItem->setName($oldDataItem['header']);
        $newItem->setEnabled(true);
        $newItem->setDescription('');
        $newItem->setId($oldDataItem['id']);

        if ($oldDataItem['parent']) {
            $newItem->setParent($currentRepository->find($oldDataItem['parent']));
        }
    }

    protected function processRootCatalog()
    {
        $catalogRepository = $this->getCommand()->getEntityManager()->getRepository('CompoCatalogBundle:Catalog');

        if (!$newCatalogItem = $catalogRepository->findOneBy(array('lvl' => 0))) {
            $newCatalogItem = new Catalog();
            $newCatalogItem->setId(1);
            $newCatalogItem->setName('Каталог');
            $newCatalogItem->setEnabled(true);
            $newCatalogItem->setDescription('');
            $this->getCommand()->changeIdGenerator($newCatalogItem);

            $this->getCommand()->getEntityManager()->persist($newCatalogItem);
            $this->getCommand()->getEntityManager()->flush();
        }
    }
}
