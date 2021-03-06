<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Process\Process;

/**
 * {@inheritdoc}
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

        if (!\is_array($paths)) {
            throw new \InvalidArgumentException('The bower setting must be an array or a configuration object.');
        }

        $vendor = $event->getComposer()->getConfig()->get('vendor-dir');

        $root_dir = \dirname($vendor) . '/';

        $pathsArray = [];

        foreach ($paths as $path) {
            $pathsArray[] = $root_dir . '/' . $path;
            $io->write(sprintf('<info>bower: %s</info>', $path));
        }

        $process = new Process('git config --global url."https://".insteadOf git://', $root_dir, null, null, 300);

        $process->run(
            function ($type, $buffer) use ($event) {
                $event->getIO()->write($buffer, false);
            }
        );

        $process = new Process('bower install --allow-root ' . implode(' ', $pathsArray), $root_dir, null, null, 300);

        $process->run(
            function ($type, $buffer) use ($event) {
                $event->getIO()->write($buffer, false);
            }
        );

        if (!$process->isSuccessful()) {
            $process = new Process('git config --global --unset url."https://".insteadOf', $root_dir, null, null, 300);

            $process->run(
                function ($type, $buffer) use ($event) {
                    $event->getIO()->write($buffer, false);
                }
            );
            throw new \RuntimeException('An error occurred when bower.');
        }

        $process = new Process('git config --global --unset url."https://".insteadOf', $root_dir, null, null, 300);

        $process->run(
            function ($type, $buffer) use ($event) {
                $event->getIO()->write($buffer, false);
            }
        );
    }
}
