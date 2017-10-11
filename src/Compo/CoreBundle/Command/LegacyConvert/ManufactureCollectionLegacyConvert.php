<?php

namespace Compo\CoreBundle\Command\LegacyConvert;


use Compo\ManufactureBundle\Entity\ManufactureCollection;
use Compo\ManufactureBundle\Entity\ManufactureCollectionAdditionalImages;

/**
 * Class ArticlesLegacyConvert
 * @package Compo\CoreBundle\Command\LegacyConvert
 */
class ManufactureCollectionLegacyConvert extends BaseLegacyConvert
{
    /**
     *
     */
    public function configure()
    {
        $this->setTableName('tovar');
        $this->setRepositoryName('CompoManufactureBundle:ManufactureCollection');
        $this->setEntityClass(ManufactureCollection::class);
    }

    /**
     * @param $oldDataItemKey
     * @param $oldDataItem
     * @param $newItem ManufactureCollection
     */
    public function iterateItem($oldDataItemKey, $oldDataItem, $newItem)
    {
        $manufactureRepository = $this->getEntityManager()->getRepository('CompoManufactureBundle:Manufacture');
        $manufactureCollectionAdditionalImagesRepository = $this->getEntityManager()->getRepository('CompoManufactureBundle:ManufactureCollectionAdditionalImages');

        $newItem->setId($oldDataItem['id']);
        $newItem->setName($oldDataItem['header']);
        $newItem->setEnabled((bool)$oldDataItem['visible']);

        $image = $newItem->getImage();

        if (!$image && $oldDataItem['picture']) {
            $picture = $this->downloadMedia($oldDataItem['picture']);

            if ($picture) {
                $newItem->setImage($picture);
            }
        }

        $newItem->setDescription($oldDataItem['body']);
        $newItem->setMetaDescription($oldDataItem['descript']);

        $newItem->setSlug(str_replace('.html', '', $oldDataItem['url']));

        if ($oldDataItem['manufacture_id']) {

            $manufacture = $manufactureRepository->find($oldDataItem['manufacture_id']);

            if ($manufacture) {
                $newItem->setManufacture($manufacture);
            }
        }

        $newItem->setCreatedAt(new \DateTime($oldDataItem['created_at']));
        $newItem->setUpdatedAt(new \DateTime($oldDataItem['created_at']));

        $oldPhotos = $this->getCommand()->getOldConnection()->fetchAll('SELECT * FROM `pages_photo` WHERE page = "product" AND item_id = ' . $newItem->getId());

        foreach ($oldPhotos as $oldDataPhotos_item) {

            $photo_media = $this->downloadMedia($oldDataPhotos_item['image_id']);

            $oldImage = $manufactureCollectionAdditionalImagesRepository->find($oldDataPhotos_item['id']);

            if ($photo_media && !$oldImage) {
                $photo = new ManufactureCollectionAdditionalImages();

                $this->getCommand()->changeIdGenerator($photo);

                $photo->setId($oldDataPhotos_item['id']);

                $photo->setManufactureCollection($newItem);
                $photo->setImage($photo_media);

                $this->getEntityManager()->persist($photo);
            }
        }

    }
}
