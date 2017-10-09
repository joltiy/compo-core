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
        /** @noinspection PhpUndefinedMethodInspection */
        $vendor = $event->getComposer()->getConfig()->get('vendor-dir');

        /** @noinspection RealpathInSteamContextInspection */
        $root_dir = realpath($vendor . '/../app/../');

        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'SyliusInstall');

        $extraParam = ' --symlink --relative';

        static::executeCommand($event, $consoleDir, 'sylius:theme:assets:install' . $extraParam, $options['process-timeout']);


        //       "Mopa\\Bundle\\BootstrapBundle\\Composer\\ScriptHandler::postInstallSymlinkTwitterBootstrap",
        // "Mopa\\Bundle\\BootstrapBundle\\Composer\\ScriptHandler::postInstallSymlinkTwitterBootstrapSass",
        static::executeCommand($event, $consoleDir, 'fos:js-routing:dump --target='.$root_dir . '/web/js/fos_js_routes.js', $options['process-timeout']);
        static::executeCommand($event, $consoleDir, 'fos:js-routing:dump --target='.$root_dir . '/web/js/fos_js_routes.js', $options['process-timeout']);

        //static::executeCommand($event, $consoleDir, 'mopa:bootstrap:symlink:sass', $options['process-timeout']);

        copy(
            $consoleDir . '/../' . 'vendor/mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle/Resources/public/sass/mopabootstrapbundle-3.2.scss',
            $consoleDir . '/../' . 'vendor/mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle/Resources/public/sass/mopabootstrapbundle.scss'

        );

        static::executeCommand($event, $consoleDir, 'assetic:dump' . ' --forks=8 --env=dev', $options['process-timeout']);
        static::executeCommand($event, $consoleDir, 'assetic:dump' . ' --forks=8 --env=prod --no-debug', $options['process-timeout']);

        static::executeCommand($event, $consoleDir, 'sonata:cache:flush-all', $options['process-timeout']);
    }
}
