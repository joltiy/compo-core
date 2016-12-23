<?php

namespace Compo\CoreBundle\Command;

use Compo\CatalogBundle\Entity\Catalog;
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
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritDoc}
 */
class ConvertImageFromOldDatabaseCommand extends ContainerAwareCommand
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
     * {@inheritdoc}
     */
    protected function configure()
    {
        ini_set('memory_limit', -1);

        $this
            ->setName('compo:convert_image_from_old_database')
            ->setDescription('Convert image from old database')
            ->addOption(
                'path',
                null,
                InputOption::VALUE_REQUIRED,
                'path'
            )->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'name'
            )
        ;

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

        $path = $input->getOption('path');
        $filename = $input->getOption('name');

        $this->em = $em;
        $this->output = $output;

        $kernel = $container->get('kernel');

        $mediaManager = $container->get('sonata.media.manager.media');

        $cache_dir = $kernel->getCacheDir();

        if (strpos($path, 'http') === false) {
            $file_path = $path;

            if (!file_exists($file_path)) {
                exit;
            }
        } else {
            $file_path = $cache_dir . '/' . $filename;

            file_put_contents($file_path, file_get_contents($path));

            if ($http_response_header[0] != 'HTTP/1.1 200 OK') {
                exit;
            }
        }

        $media = new Media();
        $media->setBinaryContent($file_path);
        $media->setContext('default');
        $media->setProviderName('sonata.media.provider.image');

        $mediaManager->save($media, true);

        exit;
    }

}
