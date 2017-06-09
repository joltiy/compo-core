<?php

namespace Compo\CoreBundle\Command;

use Compo\ArticlesBundle\Entity\Articles;
use Compo\CatalogBundle\Entity\Catalog;
use Compo\CountryBundle\Entity\Country;
use Compo\CurrencyBundle\Entity\Currency;
use Compo\FaqBundle\Entity\Faq;
use Compo\FeaturesBundle\Entity\FeatureAttribute;
use Compo\FeaturesBundle\Entity\FeatureValue;
use Compo\FeaturesBundle\Entity\FeatureVariant;
use Compo\ManufactureBundle\Entity\Manufacture;
use Compo\ManufactureBundle\Entity\ManufactureCollection;
use Compo\ManufactureBundle\Entity\ManufactureCollectionAdditionalImages;
use Compo\NewsBundle\Entity\News;
use Compo\ProductBundle\Entity\Product;
use Compo\ProductBundle\Entity\ProductAccessory;
use Compo\ProductBundle\Entity\ProductAvailability;
use Compo\ProductBundle\Entity\ProductVariation;
use Compo\Sonata\MediaBundle\Entity\Media;
use Compo\SupplierBundle\Entity\Supplier;
use Compo\UnitBundle\Entity\Unit;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritDoc}
 */
class LegacyConvertFromOldDatabaseMirplitkiCommand extends ContainerAwareCommand
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    public $em;

    /**
     * @var OutputInterface
     */
    public $output;

    /**
     * @var string
     */
    public $database_name = 'dlyavann';

    public $features = array();

    /**
     * @var \Doctrine\DBAL\Connection
     */
    public $oldConnection;

    public $oldMediaPath = 'http://www.dlyavann.ru/dbpics/';

    public $oldFilesPath = 'http://www.dlyavann.ru/files/';

    public $rootCatalog;


    public $dbpics = array();
    public $tables = array();

    public $limit = false;

    public $data = array(
        'Currency' => array(),
        'ProductAvailability' => array(),
        'Supplier' => array(),
        'Country' => array(),
        'Manufacture' => array(),
        'ManufactureCollection' => array(),
        'Catalog' => array(),
    );

    public function processRootCatalog()
    {
        $catalogRepository = $this->em->getRepository('CompoCatalogBundle:Catalog');

        if ($newCatalogItem = $catalogRepository->findOneBy(array('lvl' => 0))) {
            $this->rootCatalog = $newCatalogItem;
        } else {
            $newCatalogItem = new Catalog();
            $newCatalogItem->setName('Каталог');
            $newCatalogItem->setEnabled(true);
            $newCatalogItem->setDescription('');

            $this->em->persist($newCatalogItem);
            $this->em->flush();

            $this->rootCatalog = $newCatalogItem;
        }

    }

    public function processCatalog()
    {


        $catalogRepository = $this->em->getRepository('CompoCatalogBundle:Catalog');

        $oldCatalog = $this->oldConnection->fetchAll('SELECT * FROM `catalog` ORDER BY parent,pos');

        $newCatalog = array();


        $this->output->writeln('Catalog. Count: ' . count($oldCatalog));

        $i = 0;

        foreach ($oldCatalog as $oldCatalogItem) {
            $i++;

            if ($newCatalogItem = $catalogRepository->find($oldCatalogItem['id'])) {
                $this->output->writeln('Catalog. ' . $i . ' (OLD): ' . $oldCatalogItem['header']);
            }


            if (!$newCatalogItem) {
                $this->output->writeln('Catalog. ' . $i . ': ' . $oldCatalogItem['header']);

                $newCatalogItem = new Catalog();
            }
            $this->changeIdGenerator($newCatalogItem);

            $newCatalogItem->setName($oldCatalogItem['header']);
            $newCatalogItem->setEnabled((bool)$oldCatalogItem['visible']);
            $newCatalogItem->setDescription($oldCatalogItem['description'] . '<!--more-->' . $oldCatalogItem['description2']);


            $newCatalogItem->setSlug(str_replace('.html', '', $oldCatalogItem['url']));


            $newCatalogItem->setId($oldCatalogItem['id']);
            $newCatalogItem->setHeaderMenu($oldCatalogItem['menu_title']);

            //$newCatalogItem->setDeliveryCost($oldCatalogItem['local_delivery_cost']);


            if ($oldCatalogItem['parent']) {
                $newCatalogItem->setParent($newCatalog[$oldCatalogItem['parent']]);
            } else {
                $newCatalogItem->setParent($this->rootCatalog);
            }

            if (!$newCatalogItem->getImage() && $oldCatalogItem['pic']) {
                $newCatalogItem->setImage($this->downloadMedia($oldCatalogItem['pic']));
            }

            $newCatalog[$oldCatalogItem['id']] = $newCatalogItem;


            $this->em->persist($newCatalogItem);

        }

        $this->em->flush();
    }

    public function changeIdGenerator($newItem)
    {
        $metadata = $this->em->getClassMetaData(get_class($newItem));
        /** @noinspection PhpUndefinedMethodInspection */
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        /** @noinspection PhpUndefinedMethodInspection */
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    }

    public function downloadMedia($id)
    {
        if (isset($this->dbpics[$id]) && $this->dbpics[$id]['media_id']) {
            $container = $this->getContainer();

            $mediaManager = $container->get('sonata.media.manager.media');

            return $mediaManager->find($this->dbpics[$id]['media_id']);
        } else {
            return null;
        }
    }

    public function downloadFile($id, $oldDataPhotos_item)
    {
        try {
            $kernel = $this->getContainer()->get('kernel');
            $cache_dir = $kernel->getCacheDir();

            $mediaManager = $this->getContainer()->get('sonata.media.manager.media');

            $file_path = $cache_dir . '/' . $id;

            file_put_contents($file_path, file_get_contents($this->oldFilesPath . $id));

            if ($http_response_header[0] != 'HTTP/1.1 200 OK') {
                return null;
            }


            $media = new Media();
            $media->setBinaryContent($file_path);
            $media->setContext('default');
            $media->setProviderName('sonata.media.provider.file');
            $media->setName($id);
            $media->setDescription($oldDataPhotos_item['header']);

            $mediaManager->save($media, false);


            return $media;
        } catch (\Exception $e) {
            return null;
        }

    }

    public function processFeatures()
    {

        $featureAttributeRepositoru = $this->em->getRepository('CompoFeaturesBundle:FeatureAttribute');
        $featureVariantRepositoru = $this->em->getRepository('CompoFeaturesBundle:FeatureVariant');
        $featureValuesRepos = $this->em->getRepository('CompoFeaturesBundle:FeatureValue');

        $catalogRepos = $this->em->getRepository('CompoCatalogBundle:Catalog');


        $feature_type = $this->oldConnection->fetchAll('SELECT * FROM `feature_type` WHERE parent = 0 OR parent IS NULL ORDER BY parent,pos');


        $name = 'processFeatures';
        $i = 0;

        foreach ($feature_type as $feature_type_item) {
            $i++;

            $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $feature_type_item['header']);

            $fa = $featureAttributeRepositoru->findOneBy(array(
                'name' => $feature_type_item['header'],
                'category_id' => $feature_type_item['brunch'],
            ));

            if (!$fa) {
                $fa = new FeatureAttribute();
                $this->changeIdGenerator($fa);

                $fa->setId($feature_type_item['id']);
            }

            if ($feature_type_item['brunch']) {

                $catalog = $catalogRepos->find($feature_type_item['brunch']);

                if (!$catalog) {
                    continue;
                }


                $fa->setCatalog($catalog);

            }


            $fa->setName($feature_type_item['header']);

            if (trim(strip_tags($fa->getDescription())) == '' && trim(strip_tags($feature_type_item['description'])) != '') {
                $fa->setDescription($feature_type_item['description']);
            }

            $fa->setVisibleCard((bool)$feature_type_item['main']);
            $fa->setVisibleFilter((bool)$feature_type_item['usefilter']);
            $fa->setEnabled(1);

            if ($feature_type_item['mult']) {
                $fa->setType('variant');
            } else {
                $fa->setType('integer');
            }

            $this->em->persist($fa);

            $this->em->flush();


            $this->features[$feature_type_item['id']] = array(
                'feature' => $fa,
                'variants' => array()
            );

            $this->output->writeln('Memmory: ' . number_format((memory_get_usage()), 0, ',', ' ') . ' B');

            if ($feature_type_item['mult']) {
                $feature_values = $this->oldConnection->fetchAll('SELECT * FROM `feature_type` WHERE parent=' . $feature_type_item['id']);

                foreach ($feature_values as $feature_values_item) {

                    $fv = $featureVariantRepositoru->findOneBy(array(
                        'name' => $feature_values_item['header'],
                        'feature' => $fa
                    ));

                    if (!$fv) {
                        $fv = new FeatureVariant();
                    }

                    if (trim($fv->getDescription()) == '' && trim(strip_tags($feature_values_item['description'])) != '') {
                        $fv->setDescription($feature_values_item['description']);
                    }

                    $fv->setFeature($fa);
                    $fv->setEnabled(1);

                    $fv->setName($feature_values_item['header']);

                    $this->em->persist($fv);

                    $this->em->flush();


                    $this->features[$feature_type_item['id']]['variants'][$feature_values_item['id']] = $fv;
                }
            }
        }

        $this->output->writeln('Memmory: ' . number_format((memory_get_usage()), 0, ',', ' ') . ' B');


        $batchSize = 1000;
        $i = 0;

        /** @noinspection PhpUndefinedMethodInspection */
        $q = $this->em->createQuery('SELECT p FROM CompoProductBundle:Product p WHERE p.catalog IN(8,251,312,163,162) ORDER BY p.id ASC');

        /** @noinspection PhpUndefinedMethodInspection */
        $iterableResult = $q->iterate();

        $name = 'Features';


        $feature_bind = $this->oldConnection->fetchAll('SELECT * FROM `feature_bind`');

        $feature_bind_tovar = array();

        foreach ($feature_bind as $feature_bind_item) {
            if (!isset($feature_bind_tovar[$feature_bind_item['tovar_id']])) {
                $feature_bind_tovar[$feature_bind_item['tovar_id']] = array();
            }

            $feature_bind_tovar[$feature_bind_item['tovar_id']][] = $feature_bind_item;
        }

        unset($feature_bind);


        $detach = array();

        foreach ($iterableResult as $row_key => $row) {
            $this->output->writeln('Memmory: ' . number_format((memory_get_usage()), 0, ',', ' ') . ' B');

            /** @var Product $product */
            $product = $row[0];

            $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $product->getName());

            if (isset($feature_bind_tovar[$product->getId()])) {
                $oldProductFeatures = $feature_bind_tovar[$product->getId()];

                unset($feature_bind_tovar[$product->getId()]);
            } else {
                $oldProductFeatures = array();
            }

            $detach[] = $product;

            //$oldProductFeatures = $this->oldConnection->fetchAll('SELECT * FROM `feature_bind` WHERE tovar_id=' . $product->getId());

            foreach ($oldProductFeatures as $oldProductFeaturesItem) {

                $featureValue = $featureValuesRepos->find($oldProductFeaturesItem['id']);

                if (!$featureValue) {
                    $featureValue = new FeatureValue();

                    $featureValue->setId($oldProductFeaturesItem['id']);

                    if (isset($this->features[$oldProductFeaturesItem['feature_id']])) {
                        /** @var FeatureAttribute $featureAttribute */
                        $featureAttribute = $this->features[$oldProductFeaturesItem['feature_id']]['feature'];

                        if ($featureAttribute->getType() == 'variant') {
                            if (isset($this->features[$oldProductFeaturesItem['feature_id']]['variants'][$oldProductFeaturesItem['header']])) {
                                $featureValue->setValueVariant($this->features[$oldProductFeaturesItem['feature_id']]['variants'][$oldProductFeaturesItem['header']]);
                                $featureValue->setFeature($featureAttribute);
                                $featureValue->setProduct($product);

                                $detach[] = $featureValue;
                                $this->changeIdGenerator($featureValue);
                                $this->em->persist($featureValue);
                            }
                        } else {
                            $featureValue->setValueInteger($oldProductFeaturesItem['header']);

                            $featureValue->setFeature($featureAttribute);
                            $featureValue->setProduct($product);

                            $detach[] = $featureValue;

                            $this->changeIdGenerator($featureValue);
                            $this->em->persist($featureValue);
                        }
                    }
                } else {
                    $this->em->detach($featureValue);
                }
            }

            if (($i % $batchSize) === 0) {
                $this->em->flush();

                foreach ($detach as $detach_item) {
                    $this->em->detach($detach_item);
                }

                $detach = array();

                $this->em->clear('CompoProductBundle:Product');
                $this->em->clear('CompoFeaturesBundle:FeatureValue');

                gc_collect_cycles();


            }

            ++$i;
        }

        $this->em->flush();
        $this->em->clear('CompoProductBundle:Product');
        $this->em->clear('CompoProductBundle:FeatureValue');
    }

    public function processProductVariation()
    {
        $ProductVariationRepository = $this->em->getRepository('CompoProductBundle:ProductVariation');

        $name = 'ProductVariation';

        $currentRepository = $this->em->getRepository('CompoProductBundle:Product');

        $oldData = $this->oldConnection->fetchAll('SELECT * FROM `complect_link`');

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem) {
            $i++;
            $product = $currentRepository->find($oldDataItem['link_id']);
            $variation = $currentRepository->find($oldDataItem['tovar_id']);

            $this->output->writeln($name . '. ' . $i . ' (SKIP): ');

            if ($product && $variation) {
                if (!$ProductVariationRepository->findOneBy(array('product' => $product, 'variation' => $variation))) {
                    $productVariation = new ProductVariation();
                    $productVariation->setProduct($product);
                    $productVariation->setVariation($variation);


                    $this->em->persist($productVariation);
                }
            }


            if (($i % 200) === 0) {
                $this->em->flush();

                $this->em->clear('CompoProductBundle:Product');
                $this->em->clear('CompoProductBundle:ProductAdditionalFiles');
                $this->em->clear('CompoProductBundle:ProductAdditionalImages');

                $this->em->clear('CompoSonataMediaBundle:Media');
                $this->em->clear('SonataMediaBundle:MediaManager');

                $this->em->clear('CompoProductBundle:ProductAccessory');

                gc_enable();
                gc_collect_cycles();
            }

            $this->output->writeln('Memmory: ' . number_format((memory_get_usage()), 0, ',', ' ') . ' B');


        }
        $this->em->flush();
        $this->em->clear();

    }

    public
    function processProductAccessory()
    {
        $ProductVariationRepository = $this->em->getRepository('CompoProductBundle:ProductAccessory');

        $name = 'ProductAccessory';

        $currentRepository = $this->em->getRepository('CompoProductBundle:Product');

        $oldData = $this->oldConnection->fetchAll('SELECT * FROM `tovar_link`');

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;


        $detach = array();

        foreach ($oldData as $key => $oldDataItem) {
            $i++;
            $product = $currentRepository->find($oldDataItem['tovar_id']);
            $variation = $currentRepository->find($oldDataItem['link_id']);

            $this->output->writeln($name . '. ' . $i . ' (SKIP): ');

            if ($product && $variation) {
                if (!$ProductVariationRepository->findOneBy(array('product' => $product, 'accessory' => $variation))) {
                    $productVariation = new ProductAccessory();
                    $productVariation->setProduct($product);
                    $productVariation->setAccessory($variation);


                    $this->em->persist($productVariation);

                    $detach[] = $productVariation;
                }


            }

            unset($oldData[$key]);

            if ($product) {
                $detach[] = $product;

            }

            if ($variation) {
                $detach[] = $variation;
            }


            if (($i % 1000) === 0) {
                $this->em->flush();

                $this->em->clear('CompoProductBundle:Product');
                $this->em->clear('CompoProductBundle:ProductAdditionalFiles');
                $this->em->clear('CompoProductBundle:ProductAdditionalImages');

                $this->em->clear('CompoSonataMediaBundle:Media');
                $this->em->clear('SonataMediaBundle:MediaManager');

                $this->em->clear('CompoProductBundle:ProductAccessory');

                foreach ($detach as $item) {
                    $this->em->detach($item);
                }
                $detach = array();
                gc_enable();
                gc_collect_cycles();
            }

            $this->output->writeln('Memmory: ' . number_format((memory_get_usage()), 0, ',', ' ') . ' B');
        }


        $this->em->flush();
        $this->em->clear();
    }

    public
    function processProductVariation2()
    {
        $catalogRepository = $this->em->getRepository('CompoCatalogBundle:Catalog');

        $ProductVariationRepository = $this->em->getRepository('CompoProductBundle:ProductVariation');

        $name = 'ProductVariation2';

        $currentRepository = $this->em->getRepository('CompoProductBundle:Product');

        $oldData = $this->oldConnection->fetchAll('SELECT * FROM `complects`');

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem_key => $oldDataItem) {
            $i++;

            $newItem = $currentRepository->findOneBy(array(
                'name' => $oldDataItem['header'],
                'sku' => $oldDataItem['articul'],
                'price' => $oldDataItem['price']
            ));

            if (!$product = $currentRepository->find($oldDataItem['parent_id'])) {
                continue;
            }

            $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['header']);

            if (!$newItem) {
                $newItem = new Product();
            }

            $newItem->setName($oldDataItem['header']);
            $newItem->setSku($oldDataItem['articul']);
            $newItem->setEnabled((bool)$oldDataItem['visible']);

            if (!$newItem->getImage() && $oldDataItem['picture']) {
                $picture = $this->downloadMedia($oldDataItem['picture']);

                $newItem->setImage($picture);
            }

            $newItem->setDescription($oldDataItem['text']);

            $newItem->setPrice((int)$oldDataItem['price']);
            $newItem->setPriceOriginal((int)$oldDataItem['price']);


            $currency_code = $oldDataItem['currency'];

            $currency_code = str_replace('rur', 'RUR', $currency_code);
            $currency_code = str_replace('usd', 'USD', $currency_code);
            $currency_code = str_replace('euro', 'EUR', $currency_code);


            $newItem->setCurrency($this->em->getRepository('CompoCurrencyBundle:Currency')->findOneBy(array('code' => $currency_code)));

            if ($oldDataItem['manufacture_id']) {
                $newItem->setManufacture($this->em->getRepository('CompoManufactureBundle:Manufacture')->find($oldDataItem['manufacture_id']));

            }

            if ($oldDataItem['supplier_id']) {
                $newItem->setSupplier($this->em->getRepository('CompoSupplierBundle:Supplier')->find($oldDataItem['supplier_id']));

            }


            $newItem->setSlug(str_replace('.html', '', $oldDataItem['url']));


            $newItem->setAvailability($this->em->getRepository('CompoProductBundle:ProductAvailability')->find(10));

            $old_complect_type = $this->oldConnection->fetchAll('SELECT * FROM `complect_types` WHERE id = ' . $oldDataItem['colcat_id']);

            if ($old_complect_type) {
                $old_complect_type = $old_complect_type[0];

                if ($complect_type = $catalogRepository->findOneBy(
                    array(
                        'name' => $old_complect_type['header'],
                    )
                )
                ) {
                    $newItem->setCatalog($complect_type);
                } else {
                    $newCatalogItem = new Catalog();
                    $newCatalogItem->setName($old_complect_type['header']);
                    $newCatalogItem->setEnabled(1);
                    $newCatalogItem->setSlug($this->getContainer()->get('sonata.core.slugify.cocur')->slugify($old_complect_type['header']));
                    $newCatalogItem->setParent($catalogRepository->findOneBy(array('lvl' => 0)));

                    $metadata = $this->em->getClassMetaData(get_class($newCatalogItem));
                    /** @noinspection PhpUndefinedMethodInspection */
                    $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_AUTO);
                    /** @noinspection PhpUndefinedMethodInspection */
                    $metadata->setIdGenerator(new \Doctrine\ORM\Id\IdentityGenerator());

                    $this->em->persist($newCatalogItem);
                    $this->em->flush();

                    $newItem->setCatalog($newCatalogItem);
                }
            }

            $this->em->persist($newItem);


            $variation = $newItem;

            $this->output->writeln($name . '. ' . $i . ' (SKIP): ');

            if ($product && $variation) {
                if (!$ProductVariationRepository->findOneBy(array('product' => $product, 'variation' => $variation))) {
                    $productVariation = new ProductVariation();
                    $productVariation->setProduct($product);
                    $productVariation->setVariation($variation);


                    $this->em->persist($productVariation);

                }
            }


            if (($i % 100) === 0) {
                $this->em->flush();

                $this->em->clear('CompoProductBundle:Product');
                $this->em->clear('CompoProductBundle:ProductAdditionalFiles');
                $this->em->clear('CompoProductBundle:ProductAdditionalImages');

                $this->em->clear('CompoSonataMediaBundle:Media');
                $this->em->clear('SonataMediaBundle:MediaManager');

                $this->em->clear('CompoProductBundle:ProductAccessory');
                $this->em->clear('CompoProductBundle:ProductVariation');

                gc_collect_cycles();
            }


            $this->output->writeln('Memmory: ' . number_format((memory_get_usage()), 0, ',', ' ') . ' B');

        }

        $this->em->flush();

        $this->em->clear('CompoProductBundle:Product');
        $this->em->clear('CompoProductBundle:ProductAdditionalFiles');
        $this->em->clear('CompoProductBundle:ProductAdditionalImages');

        $this->em->clear('CompoSonataMediaBundle:Media');
        $this->em->clear('SonataMediaBundle:MediaManager');

        $this->em->clear('CompoProductBundle:ProductAccessory');
        $this->em->clear('CompoProductBundle:ProductVariation');

    }

    public
    function processProductAccessory2()
    {
        $catalogRepository = $this->em->getRepository('CompoCatalogBundle:Catalog');

        $ProductVariationRepository = $this->em->getRepository('CompoProductBundle:ProductAccessory');

        $name = 'ProductAccessory2';

        $currentRepository = $this->em->getRepository('CompoProductBundle:Product');

        $oldData = $this->oldConnection->fetchAll('SELECT * FROM `complects_additional`');

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem) {
            $i++;

            $newItem = $currentRepository->findOneBy(array(
                'name' => $oldDataItem['header'],
                'sku' => $oldDataItem['articul'],
                'price' => $oldDataItem['price']
            ));


            $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['header']);


            if (!$newItem) {
                $newItem = new Product();


            }

            $newItem->setName($oldDataItem['header']);
            $newItem->setSku($oldDataItem['articul']);
            $newItem->setEnabled((bool)$oldDataItem['visible']);

            if (!$newItem->getImage() && $oldDataItem['picture']) {
                $picture = $this->downloadMedia($oldDataItem['picture']);

                $newItem->setImage($picture);
            }

            $newItem->setDescription($oldDataItem['text']);

            $newItem->setPrice((int)$oldDataItem['price']);
            $newItem->setPriceOriginal((int)$oldDataItem['price']);


            $currency_code = $oldDataItem['currency'];

            $currency_code = str_replace('rur', 'RUR', $currency_code);
            $currency_code = str_replace('usd', 'USD', $currency_code);
            $currency_code = str_replace('euro', 'EUR', $currency_code);


            $newItem->setCurrency($this->em->getRepository('CompoCurrencyBundle:Currency')->findOneBy(array('code' => $currency_code)));

            if ($oldDataItem['manufacture_id']) {
                $newItem->setManufacture($this->em->getRepository('CompoManufactureBundle:Manufacture')->find($oldDataItem['manufacture_id']));

            }


            $newItem->setSlug($this->getContainer()->get('sonata.core.slugify.cocur')->slugify($oldDataItem['header']));


            $newItem->setAvailability($this->em->getRepository('CompoProductBundle:ProductAvailability')->find(10));


            $old_complect_type = $this->oldConnection->fetchAll('SELECT * FROM `complect_types` WHERE id = ' . $oldDataItem['colcat_id']);

            if ($old_complect_type) {
                $old_complect_type = $old_complect_type[0];

                if ($complect_type = $catalogRepository->findOneBy(
                    array(
                        'name' => $old_complect_type['header'],
                    )
                )
                ) {
                    $newItem->setCatalog($complect_type);
                } else {
                    $newCatalogItem = new Catalog();
                    $newCatalogItem->setName($old_complect_type['header']);
                    $newCatalogItem->setEnabled(1);
                    $newCatalogItem->setSlug($this->getContainer()->get('sonata.core.slugify.cocur')->slugify($old_complect_type['header']));
                    $newCatalogItem->setParent($catalogRepository->findOneBy(array('lvl' => 0)));

                    $metadata = $this->em->getClassMetaData(get_class($newCatalogItem));
                    /** @noinspection PhpUndefinedMethodInspection */
                    $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_AUTO);
                    /** @noinspection PhpUndefinedMethodInspection */
                    $metadata->setIdGenerator(new \Doctrine\ORM\Id\IdentityGenerator());


                    $this->em->persist($newCatalogItem);
                    $this->em->flush();


                    $newItem->setCatalog($newCatalogItem);
                }
            }


            $this->em->persist($newItem);


            $variation = $newItem;

            $this->output->writeln($name . '. ' . $i . ' (SKIP): ');


            $complectset = $this->oldConnection->fetchAll('SELECT * FROM `complectset` WHERE complect_id=' . $oldDataItem['id']);


            foreach ($complectset as $complectset_item) {
                $product = $currentRepository->find($complectset_item['tovar_id']);

                if ($product && $variation) {
                    if (!$ProductVariationRepository->findOneBy(array('product' => $product, 'accessory' => $variation))) {
                        $productVariation = new ProductAccessory();
                        $productVariation->setProduct($product);
                        $productVariation->setAccessory($variation);


                        $this->em->persist($productVariation);
                    }
                }
            }


            //if (($i % 100) === 0) {
            $this->em->flush();

            $this->em->clear('CompoProductBundle:Product');
            $this->em->clear('CompoProductBundle:ProductAdditionalFiles');
            $this->em->clear('CompoProductBundle:ProductAdditionalImages');

            $this->em->clear('CompoSonataMediaBundle:Media');
            $this->em->clear('SonataMediaBundle:MediaManager');

            $this->em->clear('CompoProductBundle:ProductAccessory');

            gc_enable();
            gc_collect_cycles();
            //}

            $this->output->writeln('Memmory: ' . number_format((memory_get_usage()), 0, ',', ' ') . ' B');

        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        ini_set('memory_limit', -1);

        $this
            ->setName('compo:convert_from_old_database_mirplitki')
            ->setDescription('Convert from old database')
            ->addOption(
                'host',
                null,
                InputOption::VALUE_REQUIRED,
                'Databse old host'
            )->addOption(
                'port',
                null,
                InputOption::VALUE_REQUIRED,
                'Databse old port',
                3306
            )->addOption(
                'login',
                null,
                InputOption::VALUE_REQUIRED,
                'Databse old login'
            )->addOption(
                'password',
                null,
                InputOption::VALUE_REQUIRED,
                'Databse old password'
            )->addOption(
                'database',
                null,
                InputOption::VALUE_REQUIRED,
                'Databse old name'
            )->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                'Limit',
                false
            )->addOption(
                'oldMediaPath',
                null,
                InputOption::VALUE_REQUIRED,
                'oldMediaPath',
                $this->oldMediaPath
            )->addOption(
                'tables',
                null,
                InputOption::VALUE_REQUIRED,
                'Tables',
                false
            );

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

        $this->em = $em;

        $oldHost = $input->getOption('host');
        //$oldPort = $input->getOption('port');
        $oldLogin = $input->getOption('login');
        $oldPassword = $input->getOption('password');
        $oldDatabase = $input->getOption('database');
        $this->limit = $input->getOption('limit');
        $this->oldMediaPath = $input->getOption('oldMediaPath');

        $this->tables = explode(',', $input->getOption('tables'));

        /** @var \Doctrine\Bundle\DoctrineBundle\ConnectionFactory $connectionFactory */
        $connectionFactory = $container->get('doctrine.dbal.connection_factory');
        $oldConnection = $connectionFactory->createConnection(
            array('pdo' => new \PDO("mysql:host=$oldHost;dbname=$oldDatabase", $oldLogin, $oldPassword))
        );

        $oldConnection->connect();

        $oldConnection->query('SET NAMES utf8');

        $this->em = $em;
        $this->output = $output;
        $this->oldConnection = $oldConnection;

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->oldConnection->getConfiguration()->setSQLLogger(null);

        gc_enable();
        $this->process();
    }

    public function process()
    {
        $this->processMedia();
        //$this->processRootCatalog();

        if (in_array('articles', $this->tables)) $this->processArticles();
        if (in_array('country', $this->tables)) $this->processCountry();
        if (in_array('faq', $this->tables)) $this->processFaq();
        if (in_array('manufacture', $this->tables)) $this->processManufacture();
        if (in_array('news', $this->tables)) $this->processNews();
        if (in_array('supplier', $this->tables)) $this->processSupplier();
        if (in_array('currency', $this->tables)) $this->processCurrency();
        if (in_array('unit', $this->tables)) $this->processUnit();

        if (in_array('availability', $this->tables)) $this->processProductAvailability();


        if (in_array('collection', $this->tables)) $this->processCollection();

        if (in_array('product', $this->tables)) $this->processProduct();

        //if (in_array('catalog', $this->tables)) $this->processCatalog();


        //if (in_array('features', $this->tables)) $this->processFeatures();


        //if (in_array('accessory', $this->tables)) $this->processProductVariation();

        //if (in_array('accessory', $this->tables)) $this->processProductAccessory();

        //if (in_array('accessory', $this->tables)) $this->processProductVariation2();

        //if (in_array('accessory', $this->tables)) $this->processProductAccessory2();
    }

    public function processMedia()
    {
        $db_pics = $this->oldConnection->fetchAll('SELECT * FROM db_pics ORDER BY id ASC');

        foreach ($db_pics as $db_pic) {
            $this->dbpics[$db_pic['id']] = $db_pic;
        }

        unset($db_pics);

        $i = 1;

        $media_isset = array();

        /** @noinspection PhpUndefinedMethodInspection */
        $media = $this->em->getConnection()->fetchAll('SELECT * FROM `media__media` ORDER BY id ASC ');


        foreach ($media as $item) {
            $media_isset[$item['name']] = $item['id'];
        }

        unset($media);

        foreach ($this->dbpics as $id => $item_data) {


            $this->output->writeln('Media' . '. ' . $i . ': ' . $item_data['id'] . '.' . $item_data['type']);

            $this->output->writeln('Memmory: ' . number_format((memory_get_usage()), 0, ',', ' ') . ' B');


            $item = null;

            if (isset($media_isset[$item_data['id'] . '.' . $item_data['type']])) {

                $this->dbpics[$id]['media_id'] = $media_isset[$item_data['id'] . '.' . $item_data['type']];
            } else {
                $this->dbpics[$id]['media_id'] = false;
            }

            $i++;
        }
    }

    public function processArticles()
    {
        $name = 'Articles';

        $currentRepository = $this->em->getRepository('CompoArticlesBundle:Articles');

        $oldData = $this->oldConnection->fetchAll('SELECT * FROM `articles`');

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem) {
            $i++;

            $new = false;

            if ($newItem = $currentRepository->find($oldDataItem['id'])) {
                $new = true;

                $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $oldDataItem['header']);
            }


            if (!$newItem) {
                $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['header']);

                $newItem = new Articles();
            }

            $newItem->setId($oldDataItem['id']);
            $newItem->setName($oldDataItem['header']);
            $newItem->setBody($oldDataItem['text']);

            if (!$newItem->getImage() && $oldDataItem['bank']) {
                $newItem->setImage($this->downloadMedia($oldDataItem['bank']));
            }

            $newItem->setEnabled((bool)$oldDataItem['visible']);

            $newItem->setCreatedAt(new \DateTime($oldDataItem['pdate']));


            $newItem->setDescription($oldDataItem['daily_block']);


            $slug = $this->getContainer()->get('sonata.core.slugify.cocur')->slugify($oldDataItem['header']);

            if ($new && $currentRepository->findBy(array('slug' => $slug))) {
                $slug = $i . '-' . $slug;
            } else {
                $old = $currentRepository->findBy(array('slug' => $slug));
                foreach ($old as $old_item) {
                    if ($old_item->getId() != $newItem->getId()) {
                        $slug = $i . '-' . $slug;
                    }
                }
            }

            $newItem->setSlug($slug);


            $this->changeIdGenerator($newItem);

            $this->em->persist($newItem);
            $this->em->flush();
            $this->em->clear();

        }

        $this->em->flush();
    }

    public function processCountry()
    {
        $name = 'Country';

        $currentRepository = $this->em->getRepository('CompoCountryBundle:Country');

        $oldData = $this->oldConnection->fetchAll('SELECT * FROM `countrys`');

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem) {
            $i++;

            $new = false;

            if ($newItem = $currentRepository->find($oldDataItem['id'])) {
                $new = true;

                $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $oldDataItem['header']);
            }


            if (!$newItem) {
                $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['header']);

                $newItem = new Country();
            }

            $newItem->setId($oldDataItem['id']);
            $newItem->setName($oldDataItem['header']);
            $newItem->setEnabled((bool)$oldDataItem['visible']);

            $slug = $this->getContainer()->get('sonata.core.slugify.cocur')->slugify($oldDataItem['header']);

            if ($new && $currentRepository->findBy(array('slug' => $slug))) {
                $slug = $i . '-' . $slug;
            } else {
                $old = $currentRepository->findBy(array('slug' => $slug));
                foreach ($old as $old_item) {
                    if ($old_item->getId() != $newItem->getId()) {
                        $slug = $i . '-' . $slug;
                    }
                }
            }

            $newItem->setSlug($slug);


            $countrys = array(
                'Испания' => 'ES',
                'Италия' => 'IT',
                'Россия' => 'RU',
                'Португалия' => 'PT',
                'Германия' => 'DE',
                'Китай' => 'CN',
                'Польша' => 'PL',
                'Турция' => 'TR',
                'Украина' => 'UA',
                'Беларусь' => 'BY',


            );

            $code = $countrys[$oldDataItem['header']];


            $newItem->setCode($code);

            $newItem->setDescription($oldDataItem['body']);


            $newItem->setHeader($oldDataItem['h1']);
            $newItem->setTitle($oldDataItem['title']);
            $newItem->setMetaDescription($oldDataItem['descript']);
            $newItem->setMetaKeyword($oldDataItem['keywords']);

            $this->changeIdGenerator($newItem);

            $this->em->persist($newItem);
            $this->em->flush();
            $this->em->clear();

        }

        $this->em->flush();
    }

    public function processFaq()
    {
        $name = 'Faq';

        $currentRepository = $this->em->getRepository('CompoFaqBundle:Faq');

        $oldData = $this->oldConnection->fetchAll('SELECT * FROM `faq`');

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem) {
            $i++;


            $new = false;

            if ($newItem = $currentRepository->find($oldDataItem['id'])) {
                $new = true;

                $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $oldDataItem['header']);
            }


            if (!$newItem) {
                $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['header']);

                $newItem = new Faq();
            }

            $newItem->setId($oldDataItem['id']);

            $newItem->setName($oldDataItem['header']);

            $newItem->setAnswer($oldDataItem['text']);

            if (!$newItem->getImage() && $oldDataItem['bank']) {
                $newItem->setImage($this->downloadMedia($oldDataItem['bank']));
            }

            $newItem->setEnabled((bool)$oldDataItem['visible']);

            $newItem->setCreatedAt(new \DateTime($oldDataItem['pdate']));


            $slug = $this->getContainer()->get('sonata.core.slugify.cocur')->slugify($oldDataItem['header']);

            if ($new && $currentRepository->findBy(array('slug' => $slug))) {
                $slug = $i . '-' . $slug;
            } else {
                $old = $currentRepository->findBy(array('slug' => $slug));
                foreach ($old as $old_item) {
                    if ($old_item->getId() != $newItem->getId()) {
                        $slug = $i . '-' . $slug;
                    }
                }
            }

            $newItem->setSlug($slug);

            $this->changeIdGenerator($newItem);

            $this->em->persist($newItem);
            $this->em->flush();
            $this->em->clear();


        }

        $this->em->flush();
    }

    public function processManufacture()
    {
        $countryRepository = $this->em->getRepository('CompoCountryBundle:Country');

        $name = 'Manufacture';

        $currentRepository = $this->em->getRepository('CompoManufactureBundle:Manufacture');

        $oldData = $this->oldConnection->fetchAll('SELECT * FROM `manufacture`');

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem) {
            $i++;

            $new = true;

            if ($newItem = $currentRepository->find($oldDataItem['id'])) {
                $new = false;

                $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $oldDataItem['header']);
            }

            if (!$newItem) {
                $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['header']);

                $newItem = new Manufacture();
            }

            $newItem->setId($oldDataItem['id']);
            $newItem->setName($oldDataItem['header']);
            $newItem->setEnabled((bool)$oldDataItem['visible']);

            if ($oldDataItem['mini']) {
                $newItem->setDescription($oldDataItem['mini'] . '<!--more-->' . $oldDataItem['body']);
            } else {
                $newItem->setDescription($oldDataItem['body']);
            }


            $slug = $this->getContainer()->get('sonata.core.slugify.cocur')->slugify($oldDataItem['header']);

            if ($new && $currentRepository->findBy(array('slug' => $slug))) {
                $slug = $i . '-' . $slug;
            } else {
                $old = $currentRepository->findBy(array('slug' => $slug));
                foreach ($old as $old_item) {
                    if ($old_item->getId() != $newItem->getId()) {
                        $slug = $i . '-' . $slug;
                    }
                }
            }

            $newItem->setSlug($slug);


            if (!$newItem->getImage() && $oldDataItem['brunch']) {
                $newItem->setImage($this->downloadMedia($oldDataItem['brunch']));
            }


            if ($oldDataItem['flags']) {
                $country = $countryRepository->find($oldDataItem['flags']);

                if ($country) {
                    $newItem->setCountry($country);
                }
            }

            $this->changeIdGenerator($newItem);


            $this->em->persist($newItem);
            $this->em->flush();
            $this->em->clear();

        }

        $this->em->flush();
    }


    // Дополнительные комплектации (старые): complects_additional - complectset

    // colcat_id
    // params

    public function processNews()
    {
        $name = 'News';

        $currentRepository = $this->em->getRepository('CompoNewsBundle:News');

        $oldData = $this->oldConnection->fetchAll('SELECT * FROM `news`');

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem) {
            $i++;

            $new = false;

            if ($newItem = $currentRepository->find($oldDataItem['id'])) {
                $new = true;

                $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $oldDataItem['header']);
            }


            if (!$newItem) {
                $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['header']);

                $newItem = new News();
            }

            $newItem->setId($oldDataItem['id']);
            $newItem->setName($oldDataItem['header']);

            $newItem->setBody($oldDataItem['text']);
            $newItem->setCreatedAt(new \DateTime($oldDataItem['pdate']));

            if (!$newItem->getImage() && $oldDataItem['bank']) {
                $newItem->setImage($this->downloadMedia($oldDataItem['bank']));
            }

            $newItem->setEnabled((bool)1);


            $newItem->setDescription($oldDataItem['daily_block']);


            $slug = $this->getContainer()->get('sonata.core.slugify.cocur')->slugify($oldDataItem['header']);

            if ($new && $currentRepository->findBy(array('slug' => $slug))) {
                $slug = $i . '-' . $slug;
            } else {
                $old = $currentRepository->findBy(array('slug' => $slug));
                foreach ($old as $old_item) {
                    if ($old_item->getId() != $newItem->getId()) {
                        $slug = $i . '-' . $slug;
                    }
                }
            }

            $newItem->setSlug($slug);


            $this->changeIdGenerator($newItem);

            $this->em->persist($newItem);
            $this->em->flush();
            $this->em->clear();

        }

        $this->em->flush();
    }

    // Комплектации (старые): Товар - варианты

    public function processSupplier()
    {
        $name = 'Supplier';

        $currentRepository = $this->em->getRepository('CompoSupplierBundle:Supplier');

        $oldData = $this->oldConnection->fetchAll('SELECT * FROM `suppliers`');

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem) {
            $i++;

            $new = false;

            if ($newItem = $currentRepository->find($oldDataItem['id'])) {
                $new = true;

                $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $oldDataItem['header']);
            }

            if (!$newItem) {
                $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['header']);

                $newItem = new Supplier();
            }


            $newItem->setName($oldDataItem['header']);
            $newItem->setEnabled(true);

            $newItem->setId($oldDataItem['id']);

            $this->changeIdGenerator($newItem);


            $this->em->persist($newItem);
        }

        $this->em->flush();
    }


    // Акксесуары - Доп. комплектации 2.0: Товар - Товар

    public function processCurrency()
    {
        $currencyArray = array(
            array(
                'id' => 1,
                'name' => 'Рубль',
                'description' => 'Российский рубль',
                'symbol' => 'р.',
                'sign' => '&#8381;',
                'code' => 'RUB',
                'rate' => '1',
            ),
            array(
                'id' => 2,
                'name' => 'Доллар',
                'description' => 'Доллар США',
                'symbol' => '$',
                'sign' => '$',
                'code' => 'USD',
                'rate' => '66',
            ),
            array(
                'id' => 3,
                'name' => 'Евро',
                'description' => 'Евро',
                'symbol' => 'евро',
                'sign' => '&#8364;',
                'code' => 'EUR',
                'rate' => '77',
            )
        );

        $name = 'Currency';

        $currentRepository = $this->em->getRepository('CompoCurrencyBundle:Currency');

        $oldData = $currencyArray;

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem) {
            $i++;

            if ($newItem = $currentRepository->find($oldDataItem['id'])) {
                $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $oldDataItem['name']);
            }


            if (!$newItem) {
                $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['name']);

                $newItem = new Currency();
            }

            $newItem->setName($oldDataItem['name']);
            $newItem->setId($oldDataItem['id']);
            $newItem->setCode($oldDataItem['code']);
            $newItem->setDescription($oldDataItem['description']);
            $newItem->setSymbol($oldDataItem['symbol']);
            $newItem->setSign($oldDataItem['sign']);
            $newItem->setRate($oldDataItem['rate']);

            $this->changeIdGenerator($newItem);

            $this->em->persist($newItem);

        }

        $this->em->flush();
    }

// Варианты - Комплектации 2.0: Товар - Товар

    public function processUnit()
    {
        $currencyArray = array(
            array(
                'id' => 1,
                'name' => 'Штука',
                'short_name_national' => 'шт',
                'literalNameNational' => 'ШТ',
                'shortNameInternational' => 'pc',
                'literalNameInternational' => 'PCE',
            ),
            array(
                'id' => 2,
                'name' => 'Комплект',
                'short_name_national' => 'компл',
                'literalNameNational' => 'КОМПЛ',
                'shortNameInternational' => 'set',
                'literalNameInternational' => 'SET',
            ),
            array(
                'id' => 3,
                'name' => 'Квадратный метр',
                'short_name_national' => 'м2',
                'literalNameNational' => 'М2',
                'shortNameInternational' => 'm2',
                'literalNameInternational' => 'MTK',
            ),
            array(
                'id' => 4,
                'name' => 'Сантиметр',
                'short_name_national' => 'см',
                'literalNameNational' => 'СМ',
                'shortNameInternational' => 'cm',
                'literalNameInternational' => 'CMT',
            ),
            array(
                'id' => 5,
                'name' => 'Метр',
                'short_name_national' => 'м',
                'literalNameNational' => 'М',
                'shortNameInternational' => 'm',
                'literalNameInternational' => 'MTR',
            ),
            array(
                'id' => 6,
                'name' => 'Миллиметр',
                'short_name_national' => 'мм',
                'literalNameNational' => 'ММ',
                'shortNameInternational' => 'mm',
                'literalNameInternational' => 'MMT',
            ),
        );

        $name = 'Unit';

        $currentRepository = $this->em->getRepository('CompoUnitBundle:Unit');

        $oldData = $currencyArray;

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem) {
            $i++;

            if ($newItem = $currentRepository->find($oldDataItem['id'])) {
                $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $oldDataItem['name']);
            }


            if (!$newItem) {
                $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['name']);

                $newItem = new Unit();
            }
            $newItem->setId($oldDataItem['id']);

            $newItem->setName($oldDataItem['name']);
            $newItem->setShortNameNational($oldDataItem['short_name_national']);
            $newItem->setLiteralNameNational($oldDataItem['literalNameNational']);
            $newItem->setShortNameInternational($oldDataItem['shortNameInternational']);
            $newItem->setLiteralNameInternational($oldDataItem['literalNameInternational']);


            $this->changeIdGenerator($newItem);

            $this->em->persist($newItem);

        }

        $this->em->flush();
    }

    public function processProductAvailability()
    {
        $currencyArray = array(
            array(
                'id' => 10,
                'name' => 'В наличии',
                'color' => '',
                'alias' => '',
                'description' => '',
            ),
            array(
                'id' => 20,
                'name' => 'Предзаказ',
                'color' => '',
                'alias' => '',
                'description' => '',
            ),
            array(
                'id' => 30,
                'name' => 'Нет в наличии',
                'color' => '',
                'alias' => '',
                'description' => '',
            ),

            array(
                'id' => 40,
                'name' => 'Снято с производства',
                'color' => '',
                'alias' => '',
                'description' => '',
            ),

            array(
                'id' => 25,
                'name' => 'Предоплата',
                'color' => '',
                'alias' => '',
                'description' => '',
            ),


        );

        $name = 'ProductAvailability';

        $currentRepository = $this->em->getRepository('CompoProductBundle:ProductAvailability');

        $oldData = $currencyArray;

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem) {
            $i++;

            if ($newItem = $currentRepository->find($oldDataItem['id'])) {
                $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $oldDataItem['name']);
            }

            if (!$newItem) {
                $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['name']);

                $newItem = new ProductAvailability();
            }


            $newItem->setName($oldDataItem['name']);
            $newItem->setId($oldDataItem['id']);
            $newItem->setColor($oldDataItem['color']);
            $newItem->setDescription($oldDataItem['description']);
            $newItem->setAlias($oldDataItem['alias']);

            $this->changeIdGenerator($newItem);


            $this->em->persist($newItem);
        }

        $this->em->flush();
    }

    public function processCollection()
    {
        $manufactureRepository = $this->em->getRepository('CompoManufactureBundle:Manufacture');


        $name = 'ManufactureCollection';

        $currentRepository = $this->em->getRepository('CompoManufactureBundle:ManufactureCollection');

        $oldData = $this->oldConnection->fetchAll('SELECT * FROM `tovar`');

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        foreach ($oldData as $oldDataItem) {
            $i++;

            $new = false;

            if ($newItem = $currentRepository->find($oldDataItem['id'])) {
                $new = true;

                $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $oldDataItem['header']);
            }

            if (!$newItem) {
                $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['header']);

                $newItem = new ManufactureCollection();
            }

            $newItem->setId($oldDataItem['id']);

            $newItem->setName($oldDataItem['header']);
            $newItem->setEnabled((bool)$oldDataItem['visible']);

            if (!$newItem->getImage() && $oldDataItem['picture']) {
                $picture = $this->downloadMedia($oldDataItem['picture']);

                if ($picture) {
                    $newItem->setImage($picture);

                }
            }


            $newItem->setDescription($oldDataItem['body']);
            $newItem->setMetaDescription($oldDataItem['descript']);


            if ($oldDataItem['manufacture_id']) {

                $manufacture = $manufactureRepository->find($oldDataItem['manufacture_id']);

                if ($manufacture) {
                    $newItem->setManufacture($manufacture);
                }
            }


            $newItem->setCreatedAt(new \DateTime($oldDataItem['addate']));


            $slug = $this->getContainer()->get('sonata.core.slugify.cocur')->slugify($oldDataItem['header']);

            if ($new && $currentRepository->findBy(array('slug' => $slug))) {
                $slug = $i . '-' . $slug;
            } else {
                $old = $currentRepository->findBy(array('slug' => $slug));
                foreach ($old as $old_item) {
                    if ($old_item->getId() != $newItem->getId()) {
                        $slug = $i . '-' . $slug;
                    }
                }
            }

            $newItem->setSlug($slug);


            $this->changeIdGenerator($newItem);

            $files = array();
            $photos = array();


            $ProductAdditionalImagesRepository = $this->em->getRepository('CompoManufactureBundle:ManufactureCollectionAdditionalImages');
            $ProductAdditionalFilesRepository = $this->em->getRepository('CompoManufactureBundle:ManufactureCollectionAdditionalFiles');


            $oldDataPhotos = $this->oldConnection->fetchAll('SELECT * FROM `tovar_images` WHERE  tovar_id = ' . $newItem->getId());

            foreach ($oldDataPhotos as $oldDataPhotos_item) {

                $isset_photo = $ProductAdditionalImagesRepository->findOneBy(array(
                    'id' => $oldDataPhotos_item['id'],
                    // 'product' => $newItem
                ));

                if (!$isset_photo) {
                    $photo_media = $this->downloadMedia($oldDataPhotos_item['pic_id']);
                    if ($photo_media) {
                        $photo = new ManufactureCollectionAdditionalImages();

                        $this->changeIdGenerator($photo);
                        $photo->setId($oldDataPhotos_item['id']);
                        $photo->setManufactureCollection($newItem);
                        $photo->setImage($photo_media);

                        $photos[] = $photo;
                        $deatach[] = $photo_media;

                        $deatach[] = $photo;
                    }

                } else {
                    $deatach[] = $isset_photo;

                }


            }


            foreach ($photos as $photos_item) {
                $this->em->persist($photos_item);
            }


            $this->em->persist($newItem);
            $this->em->flush();
            $this->em->clear();
        }

        $this->em->flush();
    }

    public function processProduct()
    {
        $ProductAdditionalImagesRepository = $this->em->getRepository('CompoProductBundle:ProductAdditionalImages');
        $ProductAdditionalFilesRepository = $this->em->getRepository('CompoProductBundle:ProductAdditionalFiles');

        $feature_values = $this->oldConnection->fetchAll('SELECT * FROM `colcats` ORDER BY id ASC ');


        $featureAttributeRepositoru = $this->em->getRepository('CompoFeaturesBundle:FeatureAttribute');
        $featureVariantRepositoru = $this->em->getRepository('CompoFeaturesBundle:FeatureVariant');
        $featureValuesRepos = $this->em->getRepository('CompoFeaturesBundle:FeatureValue');
        $catalogRepos = $this->em->getRepository('CompoCatalogBundle:Catalog');

        $catalog = $catalogRepos->find(2);

        $fa = $featureAttributeRepositoru->findOneBy(array(
            'name' => 'Тип',
            'catalog' => $catalog,
        ));


        if (!$fa) {
            $fa = new FeatureAttribute();
            $this->changeIdGenerator($fa);

            $fa->setId(2);
        }

        $fa->setCatalog($catalog);
        $fa->setName('Тип');
        $fa->setType('variant');
        $fa->setVisibleCard((bool)1);
        $fa->setVisibleFilter((bool)1);
        $fa->setVisibleCollection((bool)1);
        $fa->setEnabled(1);

        $this->em->persist($fa);

        $this->em->flush();

        $this->features[2] = array(
            'feature' => $fa,
            'variants' => array()
        );

        foreach ($feature_values as $feature_values_item) {

            $fv = $featureVariantRepositoru->findOneBy(array(
                'name' => $feature_values_item['header'],
                'feature' => $fa
            ));

            if (!$fv) {
                $fv = new FeatureVariant();
            }


            $fv->setFeature($fa);
            $fv->setEnabled(1);

            $fv->setName($feature_values_item['header']);

            $this->em->persist($fv);

            $this->em->flush();


            $this->features[2]['variants'][$feature_values_item['id']] = $fv;
        }


        $name = 'Product';

        $currentRepository = $this->em->getRepository('CompoProductBundle:Product');

        if ($this->limit) {
            $limit = ' LIMIT 0,' . $this->limit;
        } else {
            $limit = '';
        }

        $oldData = $this->oldConnection->fetchAll('SELECT * FROM `complects2` ORDER BY id ASC ' . $limit);

        $this->output->writeln($name . '. Count: ' . count($oldData));

        $i = 0;

        $deatach = array();

        foreach ($oldData as $oldDataItem_key => $oldDataItem) {
            $catalog = $catalogRepos->find(2);

            $i++;
            $new = false;

            if ($newItem = $currentRepository->find($oldDataItem['id'])) {
                $new = true;

                $this->output->writeln($name . '. ' . $i . ' (OLD): ' . $oldDataItem['header']);
            }

            if (!$newItem) {
                $this->output->writeln($name . '. ' . $i . ': ' . $oldDataItem['header']);

                $newItem = new Product();


            } else {
                $metadata = $this->em->getClassMetaData(get_class($newItem));
                /** @noinspection PhpUndefinedMethodInspection */
                $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_AUTO);
                /** @noinspection PhpUndefinedMethodInspection */
                $metadata->setIdGenerator(new \Doctrine\ORM\Id\IdentityGenerator());
            }

            $this->changeIdGenerator($newItem);
            $newItem->setId($oldDataItem['id']);


            if (!$newItem->getImage() && $oldDataItem['pic']) {
                $picture = $this->downloadMedia($oldDataItem['pic']);

                if ($picture) {
                    $newItem->setImage($picture);

                }
            }
            $newItem->setEnabled((bool)$oldDataItem['visible']);


            $currency_code = $oldDataItem['currency'];

            $currency_code = str_replace('rur', 'RUB', $currency_code);
            $currency_code = str_replace('usd', 'USD', $currency_code);
            $currency_code = str_replace('euro', 'EUR', $currency_code);


            $newItem->setCurrency($this->em->getRepository('CompoCurrencyBundle:Currency')->findOneBy(array('code' => $currency_code)));


            $newItem->setPriceOriginal((int)$oldDataItem['price']);

            if (isset($oldDataItem['price_old'])) {
                $newItem->setPriceOldOriginal((int)$oldDataItem['price_old']);
            }

            $newItem->setSku($oldDataItem['art']);

            $newItem->setName($oldDataItem['header']);


            if ($oldDataItem['parent_id']) {
                $newItem->setManufactureCollection($this->em->getRepository('CompoManufactureBundle:ManufactureCollection')->find($oldDataItem['parent_id']));
            }


            $unit_code = $oldDataItem['vtype'];

            $unit_code = str_replace('шт.', 'Штука', $unit_code);
            $unit_code = str_replace('компл.', 'Комплект', $unit_code);
            $unit_code = str_replace('кв.м', 'Квадратный метр', $unit_code);
            $unit_code = str_replace('??.', 'Штука', $unit_code);

            $newItem->setUnit($this->em->getRepository('CompoUnitBundle:Unit')->findOneBy(array('name' => $unit_code)));


            //$newItem->setYandexMarketEnabled((bool)$oldDataItem['yml_export']);
            //$newItem->setSaleOnlyComplete((bool)$oldDataItem['ind']);

            //$newItem->setDiscountCart($oldDataItem['discount_percent']);


            //$newItem->setDescription($oldDataItem['body']);


            $slug = $this->getContainer()->get('sonata.core.slugify.cocur')->slugify($oldDataItem['header']);

            if ($new && $currentRepository->findBy(array('slug' => $slug))) {
                $slug = $i . '-' . $slug;
            } else {
                $old = $currentRepository->findBy(array('slug' => $slug));
                foreach ($old as $old_item) {
                    if ($old_item->getId() != $newItem->getId()) {
                        $slug = $i . '-' . $slug;
                    }
                }
            }

            $newItem->setSlug($slug);


            if (2) {
                $newItem->setCatalog($catalog);
            }

            $oldDataItem['suppliers_id'] = null;



            if ($oldDataItem['parent_id']) {
                $manufactureCollection = $this->em->getRepository('CompoManufactureBundle:ManufactureCollection')->find($oldDataItem['parent_id']);


                if ($manufactureCollection) {
                    $newItem->setManufactureCollection($manufactureCollection);
                    $newItem->setManufacture($this->em->getRepository('CompoManufactureBundle:Manufacture')->find($manufactureCollection->getManufacture()));

                }





                $oldDataTovar = $this->oldConnection->fetchAll('SELECT * FROM `tovar` WHERE id = ' . $oldDataItem['parent_id']);

                if ($oldDataTovar) {
                    $oldDataItem['suppliers_id'] = $oldDataTovar[0]['suppliers_id'];
                }


            }


            if ($oldDataItem['suppliers_id']) {
                $newItem->setSupplier($this->em->getRepository('CompoSupplierBundle:Supplier')->find($oldDataItem['suppliers_id']));
            }

            if ($oldDataItem['state']) {
                $newItem->setAvailability($this->em->getRepository('CompoProductBundle:ProductAvailability')->find($oldDataItem['state']));
            }


            $files = array();
            $photos = array();

            /*
            $oldDataPhotos = $this->oldConnection->fetchAll('SELECT * FROM `pages_photo` WHERE page="product" AND item_id = ' . $newItem->getId());

            foreach ($oldDataPhotos as $oldDataPhotos_item) {

                $isset_photo = $ProductAdditionalImagesRepository->findOneBy(array(
                    'id' => $oldDataPhotos_item['id'],
                    // 'product' => $newItem
                ));

                if (!$isset_photo) {
                    $photo_media = $this->downloadMedia($oldDataPhotos_item['image_id']);
                    if ($photo_media) {
                        $photo = new ProductAdditionalImages();

                        $this->changeIdGenerator($photo);
                        $photo->setId($oldDataPhotos_item['id']);
                        $photo->setProduct($newItem);
                        $photo->setImage($photo_media);

                        $photos[] = $photo;
                        $deatach[] = $photo_media;

                        $deatach[] = $photo;
                    }

                } else {
                    $deatach[] = $isset_photo;

                }


            }


            $oldDataPhotos = $this->oldConnection->fetchAll('SELECT * FROM `tovar_files` WHERE tovar_id = ' . $newItem->getId());

            foreach ($oldDataPhotos as $oldDataPhotos_item) {

                $isset_photo = $ProductAdditionalFilesRepository->findOneBy(array(
                    'id' => $oldDataPhotos_item['id'],
                ));

                if (!$isset_photo) {
                    $photo_media = $this->downloadFile($oldDataPhotos_item['filename'], $oldDataPhotos_item);
                    if ($photo_media) {
                        $photo = new ProductAdditionalFiles();

                        $this->changeIdGenerator($photo);
                        $photo->setId($oldDataPhotos_item['id']);

                        $photo->setProduct($newItem);
                        $photo->setFile($photo_media);

                        $files[] = $photo;

                        $deatach[] = $photo;
                        $deatach[] = $photo_media;

                    }

                } else {
                    $deatach[] = $isset_photo;

                }
            }
            */


            $deatach[] = $newItem;

            $this->em->persist($newItem);

            foreach ($photos as $photos_item) {
                $this->em->persist($photos_item);
            }

            foreach ($files as $photos_item) {
                $this->em->persist($photos_item);
            }

            $this->em->flush();

            /*
            Тип: мозаика
            Поверхность: глянцевая
            Размер: 32,7x32,7
            Единица измерения: кв.м
             */


            $params = $oldDataItem['params'];


            $paramsArray = explode("\n", $params);


            $paramsResult = array();

            // Размер: ширина длина
            foreach ($paramsArray as $paramsArray_key => $paramsArray_val) {
                $paramsArray_val_temp = explode(":", $paramsArray_val);

                if (count($paramsArray_val_temp) == 2) {
                    $paramsArray_val_temp_name = $paramsArray_val_temp[0];
                    $paramsArray_val_temp_val = $paramsArray_val_temp[1];

                    $paramsArray_val_temp_name = trim($paramsArray_val_temp_name);
                    $paramsArray_val_temp_val = trim($paramsArray_val_temp_val);

                    if ($paramsArray_val_temp_name && $paramsArray_val_temp_val) {
                        $paramsResult[] = array(
                            'name' => $paramsArray_val_temp_name,
                            'val' => $paramsArray_val_temp_val,
                        );
                    }

                }
            }

            $paramsResultFinal = array();

            foreach ($paramsResult as $paramsResult_key => $paramsResult_val) {

                if ($paramsResult_val['name'] == 'Размер') {
                    $paramsResult_val_tmp = $paramsResult_val['val'];

                    // ru
                    $paramsResult_val_tmp = str_replace('х', '*', $paramsResult_val_tmp);
                    // en
                    $paramsResult_val_tmp = str_replace('x', '*', $paramsResult_val_tmp);


                    $paramsResult_val_tmp = str_replace('.', ',', $paramsResult_val_tmp);


                    $paramsResult_val_tmp_array = explode('*', $paramsResult_val_tmp);

                    foreach ($paramsResult_val_tmp_array as $paramsResult_val_tmp_array_i => $paramsResult_val_tmp_array_val) {

                        if ($paramsResult_val_tmp_array_i == 0) {
                            $paramsResultFinal[] = array(
                                'type' => 'decimal',
                                'val' => $paramsResult_val_tmp_array_val,
                                'name' => 'Ширина',
                                'unit' => 'cm',
                            );
                        }

                        if ($paramsResult_val_tmp_array_i == 1) {
                            $paramsResultFinal[] = array(
                                'type' => 'decimal',
                                'val' => $paramsResult_val_tmp_array_val,
                                'name' => 'Длина',
                                'unit' => 'cm',
                            );
                        }

                        if ($paramsResult_val_tmp_array_i == 2) {
                            $paramsResultFinal[] = array(
                                'type' => 'decimal',
                                'val' => $paramsResult_val_tmp_array_val,
                                'name' => 'Высота',
                                'unit' => 'cm',
                            );
                        }
                    }


                } else {
                    $paramsResult_val_tmp = explode(',', $paramsResult_val['val']);

                    $paramsResult[$paramsResult_key]['val'] = array();
                    $paramsResult[$paramsResult_key]['type'] = 'variant';

                    foreach ($paramsResult_val_tmp as $paramsResult_val_tmp_temp) {

                        $paramsResult_val_tmp_temp = trim($paramsResult_val_tmp_temp);

                        if ($paramsResult_val_tmp_temp) {
                            $paramsResult[$paramsResult_key]['val'][] = $paramsResult_val_tmp_temp;

                        }
                    }

                    $paramsResultFinal[] = $paramsResult[$paramsResult_key];
                }
            }


            foreach ($paramsResultFinal as $paramsResultFinalItem) {
                $fa = $featureAttributeRepositoru->findOneBy(array(
                    'name' => $paramsResultFinalItem['name'],
                    'catalog' => $catalog,
                ));

                if (!$fa) {
                    $fa = new FeatureAttribute();
                    $fa->setCatalog($catalog);
                    $fa->setName($paramsResultFinalItem['name']);

                    $fa->setVisibleCard((bool)1);
                    $fa->setVisibleFilter((bool)1);
                    $fa->setVisibleCollection((bool)1);
                    $fa->setEnabled(1);
                    $fa->setType($paramsResultFinalItem['type']);

                    $this->em->persist($fa);
                    $this->em->flush();
                }

                if ($fa->getType() == 'variant') {
                    foreach ($paramsResultFinalItem['val'] as $paramsResultFinalItemVariant) {
                        $fv = $featureVariantRepositoru->findOneBy(array(
                            'name' => $paramsResultFinalItemVariant,
                            'feature' => $fa
                        ));

                        if (!$fv) {
                            $fv = new FeatureVariant();
                            $fv->setFeature($fa);
                            $fv->setEnabled(1);
                            $fv->setName($paramsResultFinalItemVariant);
                            $this->em->persist($fv);

                            $this->em->flush();
                        }

                        $featureValue = $featureValuesRepos->findOneBy(array(
                            'product' => $newItem,
                            'feature' => $fa,
                            'valueVariant' => $fv
                        ));

                        if (!$featureValue) {
                            $featureValue = new FeatureValue();

                            $featureValue->setValueVariant($fv);
                            $featureValue->setFeature($fa);
                            $featureValue->setProduct($newItem);
                            $this->em->persist($featureValue);
                            $this->em->flush();
                        }

                    }
                } else {
                    $featureValue = $featureValuesRepos->findOneBy(array(
                        'product' => $newItem,
                        'feature' => $fa,
                        'valueDecimal' => $paramsResultFinalItem['val']
                    ));

                    if (!$featureValue) {
                        $featureValue = new FeatureValue();

                        $featureValue->setValueDecimal($paramsResultFinalItem['val']);
                        $featureValue->setFeature($fa);
                        $featureValue->setProduct($newItem);
                        $this->em->persist($featureValue);
                        $this->em->flush();
                    }
                }

            }


            $this->output->writeln('Memmory: ' . number_format((memory_get_usage()), 0, ',', ' ') . ' B');


            $this->em->flush();
            $this->em->clear();
            $this->getContainer()->get('doctrine')->resetManager();
            $this->getContainer()->get('sonata.media.manager.media')->getObjectManager()->clear();

            gc_collect_cycles();

            $this->output->writeln('Memmory flush: ' . number_format((memory_get_usage()), 0, ',', ' ') . ' B');

            foreach ($deatach as $deatach_item) {
                $this->em->detach($deatach_item);
            }

            $deatach = array();


            $oldData[$oldDataItem_key] = null;
            unset($oldData[$oldDataItem_key]);

            $this->em->flush();
            $this->em->clear();
        }

        $this->em->flush();
        $this->em->clear();
        gc_collect_cycles();

    }
}
