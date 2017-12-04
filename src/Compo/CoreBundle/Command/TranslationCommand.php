<?php

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
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $kernel = $container->get('kernel');

        $bundles = $container->getParameter('kernel.bundles');

        $domains = array('messages');

        foreach ($bundles as $key => $class) {
            $application = new Application($kernel);

            $application->setAutoExit(false);

            if (preg_match('/Compo.*/', $key) && 0 === preg_match('/^CompoSonata.*/', $key)) {
                $resources = $kernel->locateResource('@' . $key . '/Resources');

                $bundle_dir = realpath($kernel->locateResource('@' . $key . '/Resources') . '/../');

                $output_dir = $resources . '/translations';

                if (!file_exists($output_dir)) {
                    mkdir($output_dir);
                }

                $arguments = array(
                    'command' => 'translation:extract',
                    'locales' => array('ru'),
                    '--enable-extractor' => array('compo_admin'),
                    '--config' => 'app',
                    '--bundle' => $key,
                    '--output-dir' => $output_dir,
                    '--output-format' => 'yml',
                    '--default-output-format' => 'yml',
                    '--dir' => array($bundle_dir),

                    '--keep',
                    '--domain' => array($key),
                    '--ignore-domain' => $domains,
                );

                $domains[] = $key;

                $greetInput = new ArrayInput($arguments);

                $greetInput->setInteractive(false);

                $application->run($greetInput, $output);
            }
        }

        return 0;
    }
}
