<?php

namespace Compo\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ProjectVersionCreateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('compo:core:project:version:create')
            ->setDescription('compo:core:project:version:create');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        $application = $this->getApplication();

        $kernel = $application->getKernel();

        $output->writeln($this->getApplication()->getLongVersion());
        $output->writeln(sprintf('%s <info>%s</info>', $kernel->getProjectName(), $kernel->getProjectVersion()));
        $output->writeln('');

        $version_parts = explode('.', $kernel->getProjectVersion());

        $new_version_parts = $version_parts;

        $new_version_parts[2] = trim($new_version_parts[2]) + 1;

        $new_version = implode('.', $new_version_parts);

        $output->writeln(sprintf('New version: <info>%s</info>', $new_version));

        file_put_contents($kernel->getProjectVersionPath(), $new_version);

        $helper = $this->getHelper('process');

        $command = 'git -c core.quotepath=false -c log.showSignature=false commit --only -m "Update version: v' . $new_version . '" -- VERSION';

        $helper->run($output, $command, 'The process failed :(', function ($type, $data) {
            if (Process::ERR === $type) {
                exit;
            }
        });

        $command = 'git -c core.quotepath=false -c log.showSignature=false push --progress --porcelain origin refs/heads/develop:develop --tags 2>&1';

        $helper->run($output, $command, 'The process failed :(', function ($type, $data) {
            if (Process::ERR === $type) {
                exit;
            }
        });

        $command = 'git -c core.quotepath=false -c log.showSignature=false flow release start -F v' . $new_version . ' 2>&1';

        $helper->run($output, $command, 'The process failed :(', function ($type, $data) {
            if (Process::ERR === $type) {
                exit;
            }
        });

        $command = 'git -c core.quotepath=false -c log.showSignature=false flow release finish -F -p -m "Tagging version v' . $new_version . '" v' . $new_version . ' 2>&1';

        $helper->run($output, $command, 'The process failed :(', function ($type, $data) {
            if (Process::ERR === $type) {
                exit;
            }
        });
    }
}
