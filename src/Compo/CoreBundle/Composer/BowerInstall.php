<?php

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Process\Process;

/**
 * {@inheritDoc}
 */
class BowerInstall extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function process(Event $event)
    {
        $io = $event->getIO();

        $extras = $event->getComposer()->getPackage()->getExtra();

        if (!isset($extras['bower'])) {
            throw new \InvalidArgumentException('The parameter handler needs to be configured through the extra.bower setting.');
        }

        $paths = $extras['bower'];

        if (!is_array($paths)) {
            throw new \InvalidArgumentException('The bower setting must be an array or a configuration object.');
        }

        $vendor = $event->getComposer()->getConfig()->get('vendor-dir');

        $root_dir = realpath($vendor . '/../');

        foreach ($paths as $path) {
            $io->write(sprintf('<info>bower: %s</info>', $path));

            $process = new Process('bower install ' . $root_dir . '/' . $path, $root_dir, null, null, 300);

            $process->run(
                function ($type, $buffer) use ($event) {
                    $event->getIO()->write($buffer, false);
                }
            );

            if (!$process->isSuccessful()) {
                throw new \RuntimeException('An error occurred when bower.');
            }
        }
    }
}
