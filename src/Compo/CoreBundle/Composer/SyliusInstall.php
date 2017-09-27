<?php

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;

/**
 * {@inheritDoc}
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

        static::executeCommand($event, $consoleDir, 'fos:js-routing:dump', $options['process-timeout']);

        static::executeCommand($event, $consoleDir, 'assetic:dump' . ' --forks=8 --env=dev', $options['process-timeout']);
        static::executeCommand($event, $consoleDir, 'assetic:dump' . ' --forks=8 --env=prod --no-debug', $options['process-timeout']);

        static::executeCommand($event, $consoleDir, 'sonata:cache:flush-all', $options['process-timeout']);
    }
}
