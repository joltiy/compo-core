<?php

use function Deployer\{server, task, run, set, get, add};

require 'recipe/symfony.php';

ini_set('date.timezone', 'Europe/Moscow');
date_default_timezone_set('Europe/Moscow');

set('copy_dirs', ['vendor']);
set('env', 'prod');
set('shared_dirs', array('app/logs', 'web/uploads'));
set('shared_files', array('app/config/parameters.yml', 'web/robots.txt'));
set('writable_dirs', array('app/cache', 'app/logs', 'web/uploads'));

set('clear_paths', []);
//set('clear_paths', ['web/app_*.php', 'web/config.php']);

set('assets', []);
//set('assets', ['web/css', 'web/images', 'web/js']);

set('dump_assets', true);
set('writable_use_sudo', false);

set('bin/php', function () {
    return get('bin_php');
});

set('timezone', 'Europe/Moscow');
date_default_timezone_set('Europe/Moscow');

task('timezone', function () {
    set('timezone', 'Europe/Moscow');
    date_default_timezone_set('Europe/Moscow');
})->desc('timezone');


task('compo:update', function () {
    run('{{bin/php}} {{release_path}}/' . trim(get('bin_dir'), '/') . '/console compo:update --env={{env}} --no-debug');

    run("cd {{deploy_path}} && ln -sfn current/web public_html");

})->desc('compo:update');

task('compo:install', function () {
    run('{{bin/php}} {{release_path}}/' . trim(get('bin_dir'), '/') . '/console compo:install --env={{env}} --no-debug');

    run("cd {{deploy_path}} && ln -sfn current/web public_html");

})->desc('compo:install');

task('compo:create-configs', function () {
    run('{{bin/php}} {{release_path}}/' . trim(get('bin_dir'), '/') . '/console compo:create-configs --env={{env}} --no-debug');
})->desc('compo:install');

task('symfony:env_vars', function () {
    $parametrs = get('parameters');

    $parametrs_array = array();

    foreach ($parametrs as $parametrs_key => $parametrs_val) {
        $parametrs_array[] = "PARAMETERS__" . strtoupper($parametrs_key). "=" . $parametrs_val;
    }

    $parametrs_array[] = 'SYMFONY_ENV=prod';

    set('env_vars', implode(' ', $parametrs_array));

})->setPrivate();

/*
desc('Restart PHP-FPM service');
task('php-fpm:restart', function () {
    // The user must have rights for restart service php7.0-fpm.service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo systemctl restart php-fpm.service');
});
after('deploy:symlink', 'php-fpm:restart');
*/

task('php-fpm:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
    run('sudo systemctl restart php7.0-fpm.service');
});

task('php-fpm:reload', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
    run('sudo systemctl reload php7.0-fpm.service');
});


task('nginx:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
    run('sudo systemctl restart nginx.service');
});

task('nginx:reload', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
    run('sudo systemctl reload nginx.service');
});

task('deploy:assetic:dump', function () {
    if (get('dump_assets')) {
        run('{{env_vars}} {{bin/php}} {{bin/console}} assetic:dump --forks=12 {{console_options}}');
    }
})->desc('Dump assets');

task('install', [
    'timezone',
    'deploy:prepare',
    'deploy:lock',
    'timezone',
    'deploy:release',
    'deploy:update_code',
    //'deploy:clear_paths',
    'deploy:create_cache_dir',
    'deploy:shared',
    //'deploy:assets',
    //'deploy:copy_dirs',
    'symfony:env_vars',
    'deploy:vendors',
    //'deploy:assets:install',
    'deploy:assetic:dump',
    //'deploy:cache:warmup',
    'deploy:writable',
    'deploy:symlink',

    'compo:install',
    'php7.0-fpm:reload',
    'nginx:reload',

    'deploy:unlock',
    'cleanup',
])->desc('Install your project');

task('deploy', [
    'timezone',
    'deploy:prepare',
    'deploy:lock',
    'timezone',
    'deploy:release',
    'deploy:update_code',
    //'deploy:clear_paths',
    'deploy:create_cache_dir',
    'deploy:shared',
    //'deploy:assets',
    //'deploy:copy_dirs',
    'symfony:env_vars',
    'deploy:vendors',
    //'deploy:assets:install',
    'deploy:assetic:dump',
    //'deploy:cache:warmup',
    'deploy:writable',
    'deploy:symlink',
    'php7.0-fpm:reload',
    'nginx:reload',
    'compo:update',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');