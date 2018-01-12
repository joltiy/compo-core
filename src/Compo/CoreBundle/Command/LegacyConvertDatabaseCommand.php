<?php

namespace Compo\CoreBundle\Command;

use Compo\CoreBundle\Command\LegacyConvert\ArticlesLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\BaseLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\CatalogLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\CountryLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\CurrencyLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\FaqLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\FeaturesLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\FeaturesValueLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\ManufactureCollectionLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\ManufactureLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\NewsLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\ProductAvailabilityLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\ProductLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\ProductTagLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\SupplierLegacyConvert;
use Compo\CoreBundle\Command\LegacyConvert\UnitLegacyConvert;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritdoc}
 */
class LegacyConvertDatabaseCommand extends BaseLegacyConvertCommand
{
    /**
     * @var array
     */
    protected $tables = [];

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
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setTables(explode(',', $input->getOption('tables')));

        parent::execute($input, $output);
    }

    protected function startProcess()
    {
        $this->processMedia();

        $this->processLegacyConvert(ArticlesLegacyConvert::class, ['main', 'articles']);
        $this->processLegacyConvert(FaqLegacyConvert::class, ['main', 'faq']);
        $this->processLegacyConvert(NewsLegacyConvert::class, ['main', 'news']);
        $this->processLegacyConvert(UnitLegacyConvert::class, ['main', 'unit']);
        $this->processLegacyConvert(ProductTagLegacyConvert::class, ['main', 'product_tag']);
        $this->processLegacyConvert(CurrencyLegacyConvert::class, ['main', 'currency']);
        $this->processLegacyConvert(ProductAvailabilityLegacyConvert::class, ['main', 'product_availability']);
        $this->processLegacyConvert(SupplierLegacyConvert::class, ['main', 'supplier']);
        $this->processLegacyConvert(CountryLegacyConvert::class, ['main', 'country']);
        $this->processLegacyConvert(CatalogLegacyConvert::class, ['main', 'catalog']);
        $this->processLegacyConvert(ManufactureLegacyConvert::class, ['main', 'manufacture']);
        $this->processLegacyConvert(ManufactureCollectionLegacyConvert::class, ['manufacture_collection']);
        $this->processLegacyConvert(ProductLegacyConvert::class, ['product']);
        $this->processLegacyConvert(FeaturesLegacyConvert::class, ['features']);
        $this->processLegacyConvert(FeaturesValueLegacyConvert::class, ['features_value']);
    }

    /**
     * @param $convertClass
     * @param $tables array
     */
    public function processLegacyConvert($convertClass, $tables)
    {
        foreach ($tables as $table) {
            if (in_array($table, $this->tables, true)) {
                /** @var BaseLegacyConvert $convert */
                $convert = new $convertClass();

                $convert->setCommand($this);
                $convert->configure();
                $convert->process();

                break;
            }
        }
    }
}
