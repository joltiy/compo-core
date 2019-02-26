<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\SeoBundle\Command;

use JMS\JobQueueBundle\Console\CronCommand;
use JMS\JobQueueBundle\Entity\Job;
use Presta\SitemapBundle\Service\DumperInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Command to dump the sitemaps to provided directory.
 *
 * @author Konstantin Tjuterev <kostik.lv@gmail.com>
 */
class SitemapsDumpCommand extends ContainerAwareCommand implements CronCommand
{
    /**
     * @param \DateTime $lastRunAt
     *
     * @return bool
     */
    public function shouldBeScheduled(\DateTime $lastRunAt)
    {
        return time() - $lastRunAt->getTimestamp() >= 60 * 60 * 12;
    }

    /**
     * @param \DateTime $lastRunAt
     *
     * @return Job
     */
    public function createCronJob(\DateTime $lastRunAt)
    {
        return new Job('compo:sitemaps:dump');
    }

    /**
     * Configure CLI command, message, options.
     */
    protected function configure()
    {
        $this->setName('compo:sitemaps:dump')
            ->setDescription('Dumps sitemaps to given location')
            ->addOption(
                'section',
                null,
                InputOption::VALUE_REQUIRED,
                'Name of sitemap section to dump, all sections are dumped by default'
            )
            ->addOption(
                'gzip',
                null,
                InputOption::VALUE_NONE,
                'Gzip sitemap'
            )
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Location where to dump sitemaps. Generated urls will not be related to this folder.',
                'web'
            );
    }

    /**
     * Code to execute for the command.
     *
     * @param InputInterface  $input  Input object from the console
     * @param OutputInterface $output Output object for the console
     *
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$targetDir = rtrim($input->getArgument('target'), '/');

        $container = $this->getContainer();
        $dumper = $container->get('presta_sitemap.dumper');
        /* @var $dumper DumperInterface */

        /** @var \Symfony\Bundle\FrameworkBundle\Console\Application $application */
        $application = $this->getApplication();

        /** @var Kernel $kernel */
        $kernel = $application->getKernel();

        $targetDir = $kernel->getProjectDir() . '/web/';

        /*$host = getenv('server_name');*/
        $host = $kernel->getContainer()->getParameter('env(SERVER_NAME)');

        $container->get('router.request_context')->setHost($host);
        $container->get('router.request_context')->setScheme('https');

        $baseUrl = $this->getBaseUrl();

        if ($input->getOption('section')) {
            $output->writeln(
                sprintf(
                    'Dumping sitemaps section <comment>%s</comment> into <comment>%s</comment> directory',
                    $input->getOption('section'),
                    $targetDir
                )
            );
        } else {
            $output->writeln(
                sprintf(
                    'Dumping <comment>all sections</comment> of sitemaps into <comment>%s</comment> directory',
                    $targetDir
                )
            );
        }
        $options = [
            'gzip' => (bool) $input->getOption('gzip'),
        ];

        $filenames = $dumper->dump($targetDir, $baseUrl, $input->getOption('section'), $options);

        if (false === $filenames) {
            $output->writeln('<error>No URLs were added to sitemap by EventListeners</error> - this may happen when provided section is invalid');

            return;
        }

        $output->writeln('<info>Created/Updated the following sitemap files:</info>');
        foreach ($filenames as $filename) {
            $output->writeln("    <comment>$filename</comment>");
        }
    }

    /**
     * @return string
     */
    private function getBaseUrl()
    {
        if ('' === $host = getenv('SERVER_NAME')) {
            throw new \RuntimeException(
                'Router host must be configured to be able to dump the sitemap, please see documentation.'
            );
        }

        // $scheme = $context->getScheme();

        $port = '';

        $scheme = 'https';

        if ('http' === $scheme && 80 !== getenv('SERVER_PORT')) {
            $port = ':' . getenv('SERVER_PORT');
        } elseif ('https' === $scheme && 443 !== getenv('SERVER_PORT')) {
            $port = ':' . getenv('SERVER_PORT');
        }

        $host = getenv('SERVER_NAME');

        return rtrim($scheme . '://' . $host . $port, '/') . '/';
    }
}
