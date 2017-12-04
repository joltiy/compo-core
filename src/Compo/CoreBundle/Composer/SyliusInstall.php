<?php

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;

/**
 * {@inheritdoc}
 */
class SyliusInstall extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function process(Event $event)
    {
        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'SyliusInstall');

        $extraParam = ' --symlink --relative';

        static::executeCommand($event, $consoleDir, 'sylius:theme:assets:install' . $extraParam, $options['process-timeout']);
    }
}
