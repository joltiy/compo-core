<?php

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;

/**
 * {@inheritdoc}
 */
class MopaBootstrapFix extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function process(Event $event)
    {
        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'MopaBootstrapFix');

        // "Mopa\\Bundle\\BootstrapBundle\\Composer\\ScriptHandler::postInstallSymlinkTwitterBootstrap",
        // "Mopa\\Bundle\\BootstrapBundle\\Composer\\ScriptHandler::postInstallSymlinkTwitterBootstrapSass",

        copy(
            $consoleDir . '/../' . 'vendor/mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle/Resources/public/sass/mopabootstrapbundle-3.2.scss',
            $consoleDir . '/../' . 'vendor/mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle/Resources/public/sass/mopabootstrapbundle.scss'
        );

        // TODO: Хрень какая-то: internal corruption of phar "/usr/bin/composer" (__HALT_COMPILER(); not found)
        //static::executeCommand($event, $consoleDir, 'mopa:bootstrap:symlink:sass', $options['process-timeout']);
    }
}
