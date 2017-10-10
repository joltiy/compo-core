<?php

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;

/**
 * {@inheritDoc}
 */
class CacheClearProd extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function process(Event $event)
    {
        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'CacheClearProd');

        static::executeCommand($event, $consoleDir, 'cache:clear' . ' --env=prod --no-debug', $options['process-timeout']);
    }
}
