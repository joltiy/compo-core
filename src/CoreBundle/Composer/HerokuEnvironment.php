<?php

namespace Compo\CoreBundle\Composer;

use /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedNamespaceInspection */
    Composer\Script\Event;

/**
 * Class HerokuEnvironment
 *
 * composer.json
 *
 * "pre-install-cmd": [
 *      "Compo\\CoreBundle\\Composer\\HerokuEnvironment::populateEnvironment"
 * ],
 *
 * "extra": {
 *      "heroku": {
 *          "framework": "symfony2",
 *          "document-root": "web",
 *          "php-config": [
 *              "date.timezone=Europe/Moscow",
 *              "display_errors=off",
 *              "short_open_tag=off"
 *          ]
 *      }
 * }
 *
 * @package Compo\CoreBundle\Composer
 */
class HerokuEnvironment
{
    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Populate Heroku environment
     *
     * @param Event $event Event
     */
    public static function populateEnvironment(/** @noinspection PhpUndefinedClassInspection */
        Event $event)
    {
        $url = getenv('CLEARDB_DATABASE_URL'); // Если MySQL
        // $url = getenv('HEROKU_POSTGRESQL_IVORY_URL'); Если установили PostgreSQL

        if ($url) {
            $url = parse_url($url);
            putenv("SYMFONY__DATABASE_HOST={$url['host']}");
            putenv("SYMFONY__DATABASE_USER={$url['user']}");
            putenv("SYMFONY__DATABASE_PASSWORD={$url['pass']}");

            $db = substr($url['path'], 1);
            putenv("SYMFONY__DATABASE_NAME={$db}");
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $io = $event->getIO();

        /** @noinspection PhpUndefinedMethodInspection */
        $io->write('CLEARDB_DATABASE_URL=' . getenv('CLEARDB_DATABASE_URL'));
    }
}