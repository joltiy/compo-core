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
