<?php

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;

/**
 * {@inheritdoc}
 */
class CompoCoreUpdate extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function process(Event $event)
    {
        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'CompoCoreUpdate');

        static::executeCommand($event, $consoleDir, 'compo:core:update' . ' ', $options['process-timeout']);
    }
}
