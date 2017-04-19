<?php

use Symfony\Component\Yaml\Yaml;




use function Deployer\{
    add, get, server, set, parse, task, run, workingPath, writeln, runLocally, download
};




/** @noinspection PhpIncludeInspection */
require 'recipe/symfony.php';

ini_set('date.timezone', 'Europe/Moscow');
date_default_timezone_set('Europe/Moscow');

set('ssh_type', 'native');
set('ssh_multiplexing', false);


/** @noinspection PhpUndefinedFunctionInspection */
set('copy_dirs', ['vendor']);
/** @noinspection PhpUndefinedFunctionInspection */
set('env', 'prod');
/** @noinspection PhpUndefinedFunctionInspection */
set('shared_dirs', array('app/logs', 'web/uploads'));
/** @noinspection PhpUndefinedFunctionInspection */
set('shared_files', array('app/config/parameters.yml', 'web/robots.txt'));
/** @noinspection PhpUndefinedFunctionInspection */
set('writable_dirs', array('app/cache', 'app/logs', 'web/uploads'));

/** @noinspection PhpUndefinedFunctionInspection */
set('clear_paths', []);
//set('clear_paths', ['web/app_*.php', 'web/config.php']);

/** @noinspection PhpUndefinedFunctionInspection */
set('assets', []);
//set('assets', ['web/css', 'web/images', 'web/js']);

/** @noinspection PhpUndefinedFunctionInspection */
set('dump_assets', true);
/** @noinspection PhpUndefinedFunctionInspection */
set('writable_use_sudo', false);

/** @noinspection PhpUndefinedFunctionInspection */
set('bin/php', function () {
    /** @noinspection PhpUndefinedFunctionInspection */
    return get('bin_php');
});

/** @noinspection PhpUndefinedFunctionInspection */
set('timezone', 'Europe/Moscow');
date_default_timezone_set('Europe/Moscow');

/** @noinspection PhpUndefinedFunctionInspection */
task('timezone', function () {
    /** @noinspection PhpUndefinedFunctionInspection */
    set('timezone', 'Europe/Moscow');
    date_default_timezone_set('Europe/Moscow');
})->desc('timezone');


/** @noinspection PhpUndefinedFunctionInspection */
task('database:sync-from-remote', function () {
    /** @noinspection PhpUndefinedFunctionInspection */
    $databasePath = "{{deploy_path}}/backup/database";
    // mysqldump -u [username] -p [database name] > [database name].sql

    run("mkdir -p " . $databasePath);

    $parametrs = get('parameters');

    $exportDatabasePath = $databasePath . "/" . $parametrs['database_name'] . ".sql";

    run("mysqldump -u " . $parametrs['database_user'] . " " . $parametrs['database_name'] . " > " . $exportDatabasePath);

    $projectDir = runLocally('pwd');

    $varDir = $projectDir . '/var/database';
    runLocally("mkdir -p " . $varDir);

    $localDatabasePath = $varDir . "/" . $parametrs['database_name'] . ".sql";

    download( $exportDatabasePath, $localDatabasePath );


    runLocally("cd " . $projectDir . " && " . " php app/console doctrine:database:drop --if-exists --force --quiet --no-interaction --no-debug");
    runLocally("cd " . $projectDir . " && " . " php app/console doctrine:database:create --if-not-exists");

    $parameters = Yaml::parse(file_get_contents($projectDir . '/app/config/parameters.yml'));

    // mysql -u[database user] -p [database name] < file.sql
    runLocally("cd " . $projectDir . " && "
        . " mysql --user=" . $parameters['parameters']['database_user']." --password=".$parameters['parameters']['database_password']
        . ' ' . $parameters['parameters']['database_name']
        . ' < '
        . $localDatabasePath
    );
})->desc('database:sync-from-remote');




/** @noinspection PhpUndefinedFunctionInspection */
task('uploads:sync-from-remote', function () {
    $projectDir = runLocally('pwd');

    download( "{{deploy_path}}/shared/web/uploads/", $projectDir . '/web/uploads/' , array('-anv'));
})->desc('uploads:sync-from-remote');

task('local:cache:clear', function (){
    $projectDir = runLocally('pwd');

    runLocally("cd " . $projectDir . " && " . " rm -rf app/cache/dev app/cache/prod");

})->desc('local:cache:clear');

task('sync-from-remote', [
    'database:sync-from-remote',
    'uploads:sync-from-remote',
    'local:cache:clear',
])->desc('sync-from-remote');





/** @noinspection PhpUndefinedFunctionInspection */
task('compo:update', function () {
    /** @noinspection PhpUndefinedFunctionInspection */
    run('{{bin/php}} {{release_path}}/' . trim(get('bin_dir'), '/') . '/console compo:update --env={{env}} --no-debug');

    run("cd {{deploy_path}} && ln -sfn current/web public_html");

})->desc('compo:update');

/** @noinspection PhpUndefinedFunctionInspection */
task('compo:install', function () {
    /** @noinspection PhpUndefinedFunctionInspection */
    run('{{bin/php}} {{release_path}}/' . trim(get('bin_dir'), '/') . '/console compo:install --env={{env}} --no-debug');

    run("cd {{deploy_path}} && ln -sfn current/web public_html");

})->desc('compo:install');

/** @noinspection PhpUndefinedFunctionInspection */
task('compo:create-configs', function () {
    /** @noinspection PhpUndefinedFunctionInspection */
    run('{{bin/php}} {{release_path}}/' . trim(get('bin_dir'), '/') . '/console compo:create-configs --env={{env}} --no-debug');
})->desc('compo:install');

/** @noinspection PhpUndefinedFunctionInspection */
task('symfony:env_vars', function () {
    /** @noinspection PhpUndefinedFunctionInspection */
    $parametrs = get('parameters');

    $parametrs_array = array();

    foreach ($parametrs as $parametrs_key => $parametrs_val) {
        $parametrs_array[] = "PARAMETERS__" . strtoupper($parametrs_key) . "=" . $parametrs_val;
    }

    $parametrs_array[] = 'SYMFONY_ENV=prod';

    /** @noinspection PhpUndefinedFunctionInspection */
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

/** @noinspection PhpUndefinedFunctionInspection */
task('php-fpm:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
    run('sudo systemctl restart php7.0-fpm.service');
});

/** @noinspection PhpUndefinedFunctionInspection */
task('php-fpm:reload', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
    run('sudo systemctl reload php7.0-fpm.service');
});


/** @noinspection PhpUndefinedFunctionInspection */
task('nginx:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
    run('sudo systemctl restart nginx.service');
});

/** @noinspection PhpUndefinedFunctionInspection */
task('nginx:reload', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
    run('sudo systemctl reload nginx.service');
});

/** @noinspection PhpUndefinedFunctionInspection */
task('deploy:assetic:dump', function () {
    /** @noinspection PhpUndefinedFunctionInspection */
    if (get('dump_assets')) {
        // php app/console sylius:theme:assets:install --symlink --relative

        run('{{env_vars}} cd {{release_path}} && {{bin/php}} {{bin/console}} sylius:theme:assets:install --symlink --relative {{console_options}}');

        run('{{env_vars}} {{bin/php}} {{bin/console}} assetic:dump {{console_options}}');
    }
})->desc('Dump assets');

/** @noinspection PhpUndefinedFunctionInspection */
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
    'php-fpm:reload',
    'nginx:reload',

    'deploy:unlock',
    'cleanup',
])->desc('Install your project');

/** @noinspection PhpUndefinedFunctionInspection */
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
    'php-fpm:reload',
    'nginx:reload',
    'compo:update',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');