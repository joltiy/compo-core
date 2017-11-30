<?php

namespace Compo\CoreBundle\Command;

use Compo\Sonata\MediaBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritdoc}
 */
class LegacyConvertImageCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        ini_set('memory_limit', -1);

        $this
            ->setName('compo:legacy:convert:image')
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
            )->addOption(
                'id',
                null,
                InputOption::VALUE_REQUIRED,
                'id',
                0
            )->addOption(
                'dry-run',
                null,
                InputOption::VALUE_REQUIRED,
                'Dry-run',
                false
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $id = $input->getOption('id');
        $path = $input->getOption('path');
        $filename = $input->getOption('name');

        $kernel = $container->get('kernel');

        $mediaManager = $container->get('sonata.media.manager.media');

        $cache_dir = $kernel->getCacheDir();

        if (false === strpos($path, 'http')) {
            $file_path = $path;

            if (!file_exists($file_path)) {
                throw new \Exception('Path not found: ' . $file_path);
            }
        } else {
            $file_path = $cache_dir . '/' . $filename;

            copy($path, $file_path);

            if ('HTTP/1.1 200 OK' !== $http_response_header[0]) {
                throw new \Exception('Path not found: ' . $path);
            }
        }

        if ($id) {
            $media = $mediaManager->find($id);
        } else {
            $media = new Media();
        }

        $media->setName($filename);
        $media->setBinaryContent($file_path);
        $media->setContext('default');
        $media->setProviderName('sonata.media.provider.image');

        $mediaManager->save($media);
    }
}
