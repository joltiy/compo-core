<?php

namespace Compo\CoreBundle\Command;

use Compo\CatalogBundle\Entity\Catalog;
use Compo\CoreBundle\Command\LegacyConvert\ArticlesLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\BaseLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\CatalogLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\CountryLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\CurrencyLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\FaqLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\FeaturesLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\ManufactureCollectionLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\ManufactureLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\NewsLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\ProductAvailabilityLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\ProductLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\ProductTagLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\SupplierLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\UnitLegacyConvert;
use Compo\CountryBundle\Entity\Country;
use Compo\CurrencyBundle\Entity\Currency;
use Compo\FeaturesBundle\Entity\FeatureAttribute;
use Compo\FeaturesBundle\Entity\FeatureValue;
use Compo\FeaturesBundle\Entity\FeatureVariant;
use Compo\ManufactureBundle\Entity\Manufacture;
use Compo\ManufactureBundle\Entity\ManufactureCollection;
use Compo\ProductBundle\Entity\Product;
use Compo\ProductBundle\Entity\ProductAccessory;
use Compo\ProductBundle\Entity\ProductAdditionalFiles;
use Compo\ProductBundle\Entity\ProductAdditionalImages;
use Compo\ProductBundle\Entity\ProductAvailability;
use Compo\ProductBundle\Entity\ProductVariation;
use Compo\Sonata\MediaBundle\Entity\Media;
use Compo\SupplierBundle\Entity\Supplier;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritDoc}
 */
class LegacyConvertDatabaseCommand extends BaseLegacyConvertCommand
{
    /**
     * @var array
     */
    protected $tables = array();

    /**
     *
     */
    protected function startProcess()
    {
        $this->processMedia();

        $this->processLegacyConvert(ArticlesLegacyConvert::class, array('main', 'articles'));
        $this->processLegacyConvert(FaqLegacyConvert::class, array('main', 'faq'));
        $this->processLegacyConvert(NewsLegacyConvert::class, array('main', 'news'));
        $this->processLegacyConvert(UnitLegacyConvert::class, array('main', 'unit'));
        $this->processLegacyConvert(ProductTagLegacyConvert::class, array('main', 'product_tag'));
        $this->processLegacyConvert(CurrencyLegacyConvert::class, array('main', 'currency'));
        $this->processLegacyConvert(ProductAvailabilityLegacyConvert::class, array('main', 'product_availability'));
        $this->processLegacyConvert(SupplierLegacyConvert::class, array('main', 'supplier'));
        $this->processLegacyConvert(CountryLegacyConvert::class, array('main', 'country'));
        $this->processLegacyConvert(CatalogLegacyConvert::class, array('main', 'catalog'));
        $this->processLegacyConvert(ManufactureLegacyConvert::class, array('main', 'manufacture'));
        $this->processLegacyConvert(ManufactureCollectionLegacyConvert::class, array('manufacture_collection'));
        $this->processLegacyConvert(ProductLegacyConvert::class, array('product'));
        $this->processLegacyConvert(FeaturesLegacyConvert::class, array( 'features'));
    }

    /**
     * @return array
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * @param array $tables
     */
    public function setTables(array $tables)
    {
        $this->tables = $tables;
    }

    /**
     * @param $convertClass
     * @param $tables array
     */
    public function processLegacyConvert($convertClass, $tables){

        foreach ($tables as $table) {
            if (in_array($table, $this->tables, true)) {
                /** @var BaseLegacyConvert $convert */
                $convert = new $convertClass;

                $convert->setCommand($this);
                $convert->configure();
                $convert->process();

                break;
            }
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setTables(explode(',', $input->getOption('tables')));

        parent::execute($input, $output);
    }
}
