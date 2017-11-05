<?php

use Symfony\Component\Yaml\Yaml;
use function Deployer\{
    commandExist, download, get, run, runLocally, set, task, upload, writeln, after, test, has
};

/** @noinspection PhpIncludeInspection */
require 'recipe/symfony.php';

ini_set('date.timezone', 'Europe/Moscow');
date_default_timezone_set('Europe/Moscow');

set('ssh_type', 'native');
set('ssh_multiplexing', true);
set('git_tty', true);
set('default_stage', 'stage');
set('writable_mode', 'chmod');
set('php_version', (float)phpversion());

// Symfony shared dirs


set('bin_dir', 'bin');
set('var_dir', 'var');

/** @noinspection PhpUndefinedFunctionInspection */
set('copy_dirs', ['vendor', 'web/vendor']);
/** @noinspection PhpUndefinedFunctionInspection */
set('env', 'prod');
/** @noinspection PhpUndefinedFunctionInspection */
set('shared_dirs', array('var/logs', 'var/sessions', 'web/assetic', 'web/uploads', 'web/media', 'web/userfiles'));
/** @noinspection PhpUndefinedFunctionInspection */
set('shared_files', array('app/config/parameters.yml', 'web/robots.txt'));
/** @noinspection PhpUndefinedFunctionInspection */
set('writable_dirs', array('var/cache', 'var/logs', 'var/sessions', 'web/uploads', 'web/media'));

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
set(
    'bin/php',
    function () {
        /** @noinspection PhpUndefinedFunctionInspection */
        return get('bin_php');
    }
);

/** @noinspection PhpUndefinedFunctionInspection */
set('timezone', 'Europe/Moscow');
date_default_timezone_set('Europe/Moscow');


task('deploy:copy_dirs', function () {
    if (has('previous_release')) {
        foreach (get('copy_dirs') as $dir) {
            if (test("[ -d {{previous_release}}/$dir ]")) {
                run("mkdir -p {{release_path}}/$dir");
                // COMPO: Disable verbose
                run("rsync -a {{previous_release}}/$dir/ {{release_path}}/$dir");
            }
        }
    }
});

/** @noinspection PhpUndefinedFunctionInspection */
task(
    'timezone',
    function () {
        /** @noinspection PhpUndefinedFunctionInspection */
        set('timezone', 'Europe/Moscow');
        date_default_timezone_set('Europe/Moscow');
    }
)->desc('timezone');


task(
    'deploy:vendors',
    function () {
        if (!commandExist('unzip')) {
            writeln('<comment>To speed up composer installation setup "unzip" command with PHP zip extension https://goo.gl/sxzFcD</comment>');
        }
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        run(
            'cd {{release_path}} && {{env_vars}} {{bin/composer}} {{composer_options}}',
            [
                'timeout' => 6800,
            ]
        );
    }
);

/** @noinspection PhpUndefinedFunctionInspection */
task(
    'database:sync-from-remote',
    function () {
        /** @noinspection PhpUndefinedFunctionInspection */
        $databasePath = '{{deploy_path}}/backup/database';
        // mysqldump -u [username] -p [database name] > [database name].sql

        run('mkdir -p ' . $databasePath);

        $parametrs = get('parameters');

        $exportDatabasePath = $databasePath . '/' . $parametrs['database_name'] . '.sql';

        run('mysqldump -u ' . $parametrs['database_user'] . ' ' . $parametrs['database_name'] . ' > ' . $exportDatabasePath);

        $projectDir = runLocally('pwd');

        $varDir = $projectDir . '/var/database';
        runLocally('mkdir -p ' . $varDir);

        $localDatabasePath = $varDir . '/' . $parametrs['database_name'] . '.sql';

        download($exportDatabasePath, $localDatabasePath);


        runLocally('cd ' . $projectDir . ' && ' . ' php bin/console doctrine:database:drop --if-exists --force --quiet --no-interaction --no-debug');
        runLocally('cd ' . $projectDir . ' && ' . ' php bin/console doctrine:database:create --if-not-exists');

        $parameters = Yaml::parse(file_get_contents($projectDir . '/app/config/parameters.yml'));

        // mysql -u[database user] -p [database name] < file.sql
        runLocally(
            'cd ' . $projectDir . ' && '
            . ' mysql --user=' . $parameters['parameters']['database_user'] . ' --password=' . $parameters['parameters']['database_password']
            . ' ' . $parameters['parameters']['database_name']
            . ' < '
            . $localDatabasePath
        );
    }
)->desc('database:sync-from-remote');


/** @noinspection PhpUndefinedFunctionInspection */
task(
    'database:backup',
    function () {
        /** @noinspection PhpUndefinedFunctionInspection */
        $databasePath = '{{deploy_path}}/current/var/database';
        // mysqldump -u [username] -p [database name] > [database name].sql

        run('mkdir -p ' . $databasePath);

        $parametrs = get('parameters');

        $exportDatabasePath = $databasePath . '/' . $parametrs['database_name'] . '_' . date('YmdHis'). '.sql';

        run('mysqldump -u ' . $parametrs['database_user'] . ' ' . $parametrs['database_name'] . ' > ' . $exportDatabasePath);
    }
)->desc('database:sync-from-remote');



/** @noinspection PhpUndefinedFunctionInspection */
task(
    'database:sync-to-remote',
    function () {

        $projectDir = runLocally('pwd');
        $varDir = $projectDir . '/var/database';
        runLocally('mkdir -p ' . $varDir);

        $parameters = Yaml::parse(file_get_contents($projectDir . '/app/config/parameters.yml'));

        $exportDatabasePath = $varDir . '/' . $parameters['parameters']['database_name'] . '.sql';

        runLocally('mysqldump -u ' . $parameters['parameters']['database_user'] . ' ' . $parameters['parameters']['database_name'] . ' > ' . $exportDatabasePath);

        run('mkdir -p {{release_path}}/var/database/');

        upload($exportDatabasePath, '{{release_path}}/var/database/' . $parameters['parameters']['database_name'] . '.sql');


        run('cd {{release_path}} && ' . ' php bin/console doctrine:database:drop --if-exists --force --quiet --no-interaction --no-debug');
        run('cd {{release_path}} && ' . ' php bin/console doctrine:database:create --if-not-exists');

        $parametrs = get('parameters');


        run(
            'cd {{release_path}} && '
            . ' mysql --user=' . $parametrs['database_user'] . ' --password=' . $parametrs['database_password']
            . ' ' . $parametrs['database_name']
            . ' < '
            . '{{release_path}}/var/database/' . $parameters['parameters']['database_name'] . '.sql'
        );


    }
)->desc('database:sync-to-remote');


/** @noinspection PhpUndefinedFunctionInspection */
task(
    'uploads:sync-from-remote',
    function () {
        $projectDir = runLocally('pwd');

        download('{{deploy_path}}/shared/web/uploads/', $projectDir . '/web/uploads/', array('-anv'));
    }
)->desc('uploads:sync-from-remote');

task(
    'local:cache:clear',
    function () {
        $projectDir = runLocally('pwd');

        runLocally('cd ' . $projectDir . ' && ' . ' rm -rf var/cache/dev var/cache/prod');

    }
)->desc('local:cache:clear');

task(
    'sync-from-remote',
    [
        'database:sync-from-remote',
        'uploads:sync-from-remote',
        'local:cache:clear',
    ]
)->desc('sync-from-remote');


/** @noinspection PhpUndefinedFunctionInspection */
task(
    'compo:core:update',
    function () {
        //run('{{bin/php}} {{release_path}}/' . trim(get('bin_dir'), '/') . '/console compo:core:update --env={{env}} --no-debug');
        //run('{{bin/php}} {{release_path}}/' . trim(get('bin_dir'), '/') . '/console fos:elastica:populate --env=dev --no-debug');
        run('cd {{release_path}} && {{env_vars}} composer run-script compo-update-prod');
        run('cd {{release_path}} && {{env_vars}} composer run-script compo-update-core');
    }
)->desc('compo:core:update');


/** @noinspection PhpUndefinedFunctionInspection */
task(
    'compo:core:install',
    function () {
        /** @noinspection PhpUndefinedFunctionInspection */
        run('{{bin/php}} {{release_path}}/' . trim(get('bin_dir'), '/') . '/console compo:core:install --env={{env}} --no-debug');


    }
)->desc('compo:core:install');

/** @noinspection PhpUndefinedFunctionInspection */
task(
    'compo:create-configs',
    function () {
        /** @noinspection PhpUndefinedFunctionInspection */
        run('{{bin/php}} {{release_path}}/' . trim(get('bin_dir'), '/') . '/console compo:create-configs --env={{env}} --no-debug');
    }
)->desc('compo:create-configs');

/** @noinspection PhpUndefinedFunctionInspection */
task(
    'symfony:env_vars',
    function () {
        /** @noinspection PhpUndefinedFunctionInspection */
        $parametrs = get('parameters');

        $parametrs_array = array();

        foreach ($parametrs as $parametrs_key => $parametrs_val) {
            $parametrs_array[] = 'PARAMETERS__' . strtoupper($parametrs_key) . '=' . $parametrs_val;
        }

        $parametrs_array[] = 'SYMFONY_ENV=prod';

        /** @noinspection PhpUndefinedFunctionInspection */
        set('env_vars', implode(' ', $parametrs_array));

    }
)->setPrivate();

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
task(
    'php-fpm:restart',
    function () {
        // The user must have rights for restart service
        // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
        run('sudo service php{{php_version}}-fpm restart');
    }
);

task(
    'behat',
    function () {
        // The user must have rights for restart service
        // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
        run('cd {{release_path}} && ' . trim(get('bin_dir'), '/') . '/behat --format html --format=pretty');
    }
);

/** @noinspection PhpUndefinedFunctionInspection */
task(
    'php-fpm:reload',
    function () {
        // The user must have rights for restart service
        // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
        run('sudo service php{{php_version}}-fpm reload');
    }
);


/** @noinspection PhpUndefinedFunctionInspection */
task(
    'nginx:restart',
    function () {
        // The user must have rights for restart service
        // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
        run('sudo service nginx restart');
    }
);

/** @noinspection PhpUndefinedFunctionInspection */
task(
    'nginx:reload',
    function () {
        // The user must have rights for restart service
        // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart nginx.service
        run('sudo service nginx reload');
    }
);

/** @noinspection PhpUndefinedFunctionInspection */
task(
    'deploy:assetic:dump',
    function () {
        /** @noinspection PhpUndefinedFunctionInspection */
        if (get('dump_assets')) {
            // php bin/console sylius:theme:assets:install --symlink --relative

            run('{{env_vars}} cd {{release_path}} && {{bin/php}} {{bin/console}} sylius:theme:assets:install --symlink --relative {{console_options}}');

            $env = get('env');

            set('env', 'dev');

            run('{{env_vars}} cd {{release_path}} && {{bin/php}} {{bin/console}} assetic:dump --env=dev');

            set('env', $env);

            run('{{env_vars}} cd {{release_path}} && {{bin/php}} {{bin/console}} assetic:dump  {{console_options}}');
        }
    }
)->desc('Dump assets');


/** @noinspection PhpUndefinedFunctionInspection */
task(
    'deploy:sitemaps',
    function () {

        $sitemapsPath = '{{deploy_path}}/backup/sitemaps';

        run("mkdir -p $sitemapsPath");

        try {
            run("cp -rf {{deploy_path}}/current/web/sitemap.* $sitemapsPath/");
            run("cp -rf $sitemapsPath/sitemap.* {{release_path}}/web/");
        } catch (\Exception $e) {

        }


    }
)->desc('deploy:sitemaps');


/** @noinspection PhpUndefinedFunctionInspection */
task(
    'deploy:market',
    function () {

        $sitemapsPath = '{{deploy_path}}/backup/market';

        run("mkdir -p $sitemapsPath");

        try {
            run("cp -rf {{deploy_path}}/current/web/yandex.market.* $sitemapsPath/");
            run("cp -rf $sitemapsPath/yandex.market.* {{release_path}}/web/");

            run("cp -rf {{deploy_path}}/current/web/google.merchant.* $sitemapsPath/");
            run("cp -rf $sitemapsPath/google.merchant.* {{release_path}}/web/");
        } catch (\Exception $e) {

        }


    }
)->desc('deploy:market');


task(
    'deploy:vendors:update',
    function () {
        if (!commandExist('unzip')) {
            writeln('<comment>To speed up composer installation setup "unzip" command with PHP zip extension https://goo.gl/sxzFcD</comment>');
        }
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        run(
            'cd {{release_path}} && {{env_vars}} {{bin/composer}} update "comporu/*" --verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader',
            [
                'timeout' => 6800,
            ]
        );
    }
);

task(
    'git:commit:composer',
    function () {
        if (test('cd {{release_path}} && git status --porcelain composer.lock|grep \'composer.lock\' > /dev/null 2>&1')) {
            run('cd {{release_path}} && {{env_vars}} git commit composer.lock -m "Composer update"');
            run('cd {{release_path}} && {{env_vars}} git push');
        }

    }
)->desc('git:commit:composer');

/** @noinspection PhpUndefinedFunctionInspection */
task(
    'deploy:dev',
    [
        'timezone',
        'deploy:prepare',
        'deploy:lock',
        'timezone',
        'deploy:release',
        'deploy:update_code',
        //'deploy:clear_paths',
        'deploy:create_cache_dir',
        'deploy:shared',
        'deploy:sitemaps',
        'deploy:market',
        //'deploy:assets',
        'deploy:copy_dirs',
        'symfony:env_vars',
        'deploy:vendors:update',
        //'deploy:assets:install',
        //'deploy:assetic:dump',
        //'deploy:cache:warmup',
        'deploy:writable',
        'compo:core:update',
        'deploy:symlink',
        'php-fpm:reload',
        'nginx:reload',
        'git:commit:composer',
        'deploy:unlock',
        'cleanup',
    ]
)->desc('Deploy dev your project');


/** @noinspection PhpUndefinedFunctionInspection */
task(
    'install',
    [
        'timezone',
        'deploy:prepare',
        'deploy:lock',
        'timezone',
        'deploy:release',
        'deploy:update_code',
        //'deploy:clear_paths',
        'deploy:create_cache_dir',
        'deploy:shared',
        'deploy:sitemaps',
        'deploy:market',
        //'deploy:assets',
        'deploy:copy_dirs',
        'symfony:env_vars',
        'deploy:vendors',
        //'deploy:assets:install',
        //'deploy:assetic:dump',
        //'deploy:cache:warmup',
        'deploy:writable',
        'compo:core:install',
        'deploy:symlink',
        'php-fpm:reload',
        'nginx:reload',
        'deploy:unlock',
        'cleanup',
    ]
)->desc('Install your project');

/** @noinspection PhpUndefinedFunctionInspection */
task(
    'deploy',
    [
        'timezone',
        'deploy:prepare',
        'deploy:lock',
        'database:backup',
        'timezone',
        'deploy:release',
        'deploy:update_code',
        //'deploy:clear_paths',
        'deploy:create_cache_dir',
        'deploy:shared',
        'deploy:sitemaps',
        'deploy:market',
        //'deploy:assets',
        'deploy:copy_dirs',
        'symfony:env_vars',
        'deploy:vendors',
        //'deploy:assets:install',
        //'deploy:assetic:dump',
        //'deploy:cache:warmup',
        'deploy:writable',
        'compo:core:update',
        'deploy:symlink',
        'php-fpm:reload',
        'nginx:reload',
        //'behat',
        'deploy:unlock',
        'cleanup',
    ]
)->desc('Deploy your project');

task('rollback:after', [
    'php-fpm:reload',
    'nginx:reload',
    'deploy:vendors',

    'compo:core:update',
    'php-fpm:reload',
    'nginx:reload',
]);

after('rollback', 'rollback:after');
