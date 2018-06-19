<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TranslationCommand.
 */
class TranslationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('compo:core:translation')
            ->setDescription('Generate translation');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $kernel = $container->get('kernel');

        /** @var array $bundles */
        $bundles = $container->getParameter('kernel.bundles');

        $domains = ['messages'];

        foreach ($bundles as $key => $class) {
            $application = new Application($kernel);

            $application->setAutoExit(false);

            /* @noinspection NotOptimalRegularExpressionsInspection */
            if (preg_match('/Compo.*/', $key) && 0 === preg_match('/^CompoSonata.*/', $key)) {
                $resources = $kernel->locateResource('@' . $key . '/Resources');

                $bundle_dir = \dirname($kernel->locateResource('@' . $key . '/Resources')) . '/';

                $output_dir = $resources . '/translations';

                if (!file_exists($output_dir) && !mkdir($output_dir) && !is_dir($output_dir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $output_dir));
                }

                $arguments = [
                    'command' => 'translation:extract',
                    'locales' => ['ru'],
                    '--enable-extractor' => ['compo_admin'],
                    '--config' => 'app',
                    '--bundle' => $key,
                    '--output-dir' => $output_dir,
                    '--output-format' => 'yml',
                    '--default-output-format' => 'yml',
                    '--dir' => [$bundle_dir],

                    '--keep',
                    '--domain' => [$key],
                    '--ignore-domain' => $domains,
                ];

                $domains[] = $key;

                $greetInput = new ArrayInput($arguments);

                $greetInput->setInteractive(false);

                $application->run($greetInput, $output);
            }
        }

        return 0;
    }
}
