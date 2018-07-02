<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class CreateConfigs.
 */
class CreateConfigs
{
    /**
     * @param Event $event Event
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function process(
        Event $event
    ) {
        $vendor = $event->getComposer()->getConfig()->get('vendor-dir');

        $project_dir = \dirname($vendor);

        if (!\file_exists($project_dir . '/.env')) {
            copy($project_dir . '/.env.dist', $project_dir . '/.env');
        }

        (new Dotenv())->load($project_dir . '/.env');

        $root_dir = \dirname($vendor) . '/app/';

        $parameters = [];

        $SYMFONY_DOTENV_VARS = explode(',', getenv('SYMFONY_DOTENV_VARS'));

        foreach ($SYMFONY_DOTENV_VARS as $DOTENV_VAR) {
            $parameters[mb_strtolower($DOTENV_VAR)] = getenv($DOTENV_VAR);
        }

        $parameters['server_root'] = \dirname($vendor) . '/web/';

        $parameters['auth_basic_user_file'] = $root_dir . '/config/htpasswd.conf';

        $loader = new \Twig_Loader_Array(
            [
                'nginx.conf.twig' => file_get_contents($root_dir . '/config/nginx.conf.twig'),
                'php-fpm.conf.twig' => file_get_contents($root_dir . '/config/php-fpm.conf.twig'),
                'servers.yml.dist' => file_get_contents($root_dir . '/config/servers.yml.dist'),

                '@CompoCore/Nginx/nginx_macro.html.twig' => file_get_contents($vendor . '/comporu/compo-core/src/Compo/CoreBundle/Resources/views/Nginx/nginx_macro.html.twig'),
            ]
        );

        $twig = new \Twig_Environment($loader, ['autoescape' => false, 'debug' => false]);

        if (!file_exists($root_dir . '/config/htpasswd.conf') || $parameters['server_user'] . ':' . crypt($parameters['server_password'], base64_encode($parameters['server_password'])) !== file_get_contents($root_dir . '/config/htpasswd.conf')) {
            file_put_contents($root_dir . '/config/htpasswd.conf', $parameters['server_user'] . ':' . crypt($parameters['server_password'], base64_encode($parameters['server_password'])));
        }

        $templateNginx = 'nginx.conf.twig';

        if (!file_exists($root_dir . '/config/nginx.conf') || file_get_contents($root_dir . '/config/nginx.conf') !== $twig->render($templateNginx, $parameters)) {
            file_put_contents($root_dir . '/config/nginx.conf', $twig->render($templateNginx, $parameters));
        }

        $templatePhpFpm = 'php-fpm.conf.twig';

        if (!file_exists($root_dir . '/config/php-fpm.conf') || file_get_contents($root_dir . '/config/php-fpm.conf') !== $twig->render($templatePhpFpm, $parameters)) {
            file_put_contents($root_dir . '/config/php-fpm.conf', $twig->render($templatePhpFpm, $parameters));
        }

        $templateServers = 'servers.yml.dist';

        if (!file_exists($root_dir . '/config/servers.yml')) {
            file_put_contents($root_dir . '/config/servers.yml', $twig->render($templateServers, $parameters));
        }
    }
}
