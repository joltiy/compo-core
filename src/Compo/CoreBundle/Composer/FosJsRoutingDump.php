<?php

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;

/**
 * {@inheritDoc}
 */
class FosJsRoutingDump extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function process(Event $event)
    {
        $vendor = $event->getComposer()->getConfig()->get('vendor-dir');

        $root_dir = realpath($vendor . '/../app/../');

        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'FosJsRoutingDump');

        static::executeCommand($event, $consoleDir, 'fos:js-routing:dump --target='.$root_dir . '/web/js/fos_js_routes.js', $options['process-timeout']);
    }
}
