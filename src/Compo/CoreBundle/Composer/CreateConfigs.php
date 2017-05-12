<?php

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Yaml\Yaml;

/**
 * Class HerokuEnvironment
 *
 * composer.json
 *
 * @package Compo\CoreBundle\Composer
 */
class CreateConfigs
{
    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Populate Heroku environment
     *
     * @param Event $event Event
     */
    public static function process(/** @noinspection PhpUndefinedClassInspection */
        Event $event)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $vendor = $event->getComposer()->getConfig()->get('vendor-dir');

        $root_dir = realpath($vendor . '/../app/');

        $parameters = Yaml::parse(file_get_contents($root_dir . '/config/parameters.yml'));
        $parameters = $parameters['parameters'];


        if (!$parameters['server_root']) {
            $parameters['server_root'] = realpath($root_dir . '/../web/');
        }

        $parameters['auth_basic_user_file'] = $root_dir . '/config/htpasswd.conf';

        $loader = new \Twig_Loader_Array(array(
            'nginx.conf.twig' => file_get_contents($root_dir . '/config/nginx.conf.twig'),
            'php-fpm.conf.twig' => file_get_contents($root_dir . '/config/php-fpm.conf.twig'),
            'servers.yml.dist' => file_get_contents($root_dir . '/config/servers.yml.dist'),
        ));

        $twig = new \Twig_Environment($loader, array('autoescape' => false, 'debug' => false));

        if (!file_exists($root_dir . '/config/htpasswd.conf') || file_get_contents($root_dir . '/config/htpasswd.conf') != $parameters['server_user'] . ':' . crypt($parameters['server_password'], base64_encode($parameters['server_password']))) {
            file_put_contents($root_dir . '/config/htpasswd.conf', $parameters['server_user'] . ':' . crypt($parameters['server_password'], base64_encode($parameters['server_password'])));
        }

        if (!file_exists($root_dir . '/config/nginx.conf') || file_get_contents($root_dir . '/config/nginx.conf') != $twig->render('nginx.conf.twig', $parameters)) {
            file_put_contents($root_dir . '/config/nginx.conf', $twig->render('nginx.conf.twig', $parameters));
        }

        if (!file_exists($root_dir . '/config/php-fpm.conf') || file_get_contents($root_dir . '/config/php-fpm.conf') != $twig->render('php-fpm.conf.twig', $parameters)) {
            file_put_contents($root_dir . '/config/php-fpm.conf', $twig->render('php-fpm.conf.twig', $parameters));
        }

        if (!file_exists($root_dir . '/config/servers.yml')) {
            file_put_contents($root_dir . '/config/servers.yml', $twig->render('servers.yml.dist', $parameters));
        }
    }
}