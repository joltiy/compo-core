<?php

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;

/**
 * {@inheritdoc}
 */
class AsseticDump extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function process(Event $event)
    {
        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'AsseticDump');

        static::executeCommand($event, $consoleDir, 'assetic:dump' . ' --env=dev', $options['process-timeout']);
        //static::executeCommand($event, $consoleDir, 'assetic:dump' . ' --forks=8 --env=prod --no-debug', $options['process-timeout']);
    }
}
