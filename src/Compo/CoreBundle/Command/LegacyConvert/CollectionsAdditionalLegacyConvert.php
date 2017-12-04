<?php

namespace Compo\CoreBundle\Command\LegacyConvert;

/**
 * Class ArticlesLegacyConvert.
 */
class CollectionsAdditionalLegacyConvert extends BaseLegacyConvert
{
    public function getOldData()
    {
        $table = 'collections_additional';

        $this->getCommand()->getIo()->note('Load from old table: ' . $table);

        $query = 'SELECT * FROM `' . $table . '` ORDER BY id ASC';

        $oldData = $this->getCommand()->getOldConnection()->fetchAll($query);

        $this->getCommand()->getIo()->note('Count: ' . count($oldData));

        return $oldData;
    }

    public function process()
    {
        $oldData = $this->getOldData();

        $this->getCommand()->getIo()->progressStart(count($oldData));

        $batchSize = $this->batchSize;

        $i = 0;

        foreach ($oldData as $oldDataItemKey => $oldDataItem) {
            ++$i;

            try {
                $queryBuilder = $this->getCommand()->getDoctrine()->getConnection()->createQueryBuilder();

                $queryBuilder
                    ->insert('product_additional_manufacture_collections')
                    ->values(
                        array(
                            'manufacture_collection_id' => '?',
                            'product_id' => '?',
                        )
                    )
                    ->setParameter(0, $oldDataItem['tovar_id'])
                    ->setParameter(1, $oldDataItem['complects_id'])
                    ->execute()
                ;
            } catch (\Exception $e) {
            }

            $this->getCommand()->getIo()->progressAdvance();
        }

        $this->getCommand()->getEntityManager()->flush();
        $this->getCommand()->getEntityManager()->clear();
        gc_collect_cycles();

        $this->getCommand()->getIo()->progressFinish();

        $this->getCommand()->getIo()->success('Load: ');
    }
}
