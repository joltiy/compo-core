<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;

/**
 * {@inheritdoc}
 */
class FosJsRoutingDump extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function process(Event $event)
    {
        $vendor = $event->getComposer()->getConfig()->get('vendor-dir');

        $root_dir = \dirname($vendor) . '/app/../';

        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'FosJsRoutingDump');

        static::executeCommand($event, $consoleDir, 'fos:js-routing:dump --target=' . $root_dir . '/web/js/fos_js_routes.js', $options['process-timeout']);
    }
}
