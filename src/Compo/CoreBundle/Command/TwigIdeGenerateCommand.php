<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class TwigIdeGenerateCommand.
 */
class TwigIdeGenerateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:core:twig:ide_generate')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectDir = $this->getContainer()->get('kernel')->getProjectDir();

        $themesDir = $projectDir . '/app/themes';

        $finder = new Finder();

        /** @var \IteratorAggregate $themesDirs */
        $themesDirs = $finder->directories()->in($themesDir)->depth('== 0');

        $ideTwigItems = [];

        /** @var SplFileInfo $themesDirsItem */
        foreach ($themesDirs as $themesDirsItem) {
            $output->writeln('Theme: ' . $themesDirsItem->getBasename());

            /** @var \IteratorAggregate $bundles */
            $bundles = $finder->directories()->in($themesDirsItem->getRealPath())->depth('== 0');

            /** @var SplFileInfo $bundlesItem */
            foreach ($bundles as $bundlesItem) {
                //dump($bundlesItem->getRealPath());
                $ideTwigItems[] = [
                    'namespace' => $bundlesItem->getBasename(),
                    'path' => 'app/themes/' . $themesDirsItem->getBasename() . '/' . $bundlesItem->getBasename() . '/views',
                    'type' => 'Bundle',
                ];
            }
        }

        $ideTwig = [
            'namespaces' => $ideTwigItems,
        ];

        $fs = new Filesystem();

        $fs->dumpFile($projectDir . '/ide-twig.json', json_encode($ideTwig, JSON_PRETTY_PRINT));
    }
}
