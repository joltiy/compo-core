<?php

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;

/**
 * {@inheritdoc}
 */
class CacheFlush extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function process(Event $event)
    {
        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'CacheFlush');

        static::executeCommand($event, $consoleDir, 'sonata:cache:flush-all', $options['process-timeout']);
    }
}
