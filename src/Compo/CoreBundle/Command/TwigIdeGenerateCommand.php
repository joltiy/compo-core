<?php

namespace Compo\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
        $themesDirs = $finder->directories()->in($themesDir)->depth('== 0');

        $ideTwigItems = array();

        /** @var SplFileInfo $themesDirsItem */
        foreach ($themesDirs as $themesDirsItem) {
            $output->writeln('Theme: ' . $themesDirsItem->getBasename());

            $bundles = $finder->directories()->in($themesDirsItem->getRealPath())->depth('== 0');

            /** @var SplFileInfo $bundlesItem */
            foreach ($bundles as $bundlesItem) {
                //dump($bundlesItem->getRealPath());
                $ideTwigItems[] = array(
                    'namespace' => $bundlesItem->getBasename(),
                    'path' => 'app/themes/' . $themesDirsItem->getBasename() . '/' . $bundlesItem->getBasename() . '/views',
                    'type' => 'Bundle',
                );
            }
        }

        $ideTwig = array(
            'namespaces' => $ideTwigItems
        );

        $fs = new Filesystem();

        $fs->dumpFile($projectDir . '/ide-twig.json', json_encode($ideTwig, JSON_PRETTY_PRINT));
    }
}
