<?php

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;

/**
 * Class TempCommands
 *
 * @package Compo\CoreBundle\Composer
 */
class TempCommands
{
    /** @noinspection PhpUndefinedClassInspection */
    /**
     *
     * https://github.com/phiamo/MopaBootstrapBundle/pull/1095
     *
     * @param Event $event Event
     */
    public static function fixPostInstallSymlinkTwitterBootstrap(
        /** @noinspection PhpUndefinedClassInspection */
        Event $event
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $vendor = $event->getComposer()->getConfig()->get('vendor-dir');

        $linkTarget = $vendor . DIRECTORY_SEPARATOR . 'mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle\Resources\public\bootstrap';

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && is_dir($linkTarget)) {
            rmdir($linkTarget);
        }
    }
}