<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config

set('repository', 'git@github.com:S-Krea/RotativBox.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('dev')
    ->setHostname('guepe.o2switch.net')
    ->setRemoteUser('ibtj9208')
    ->set('http_user', 'ibtj9208')
    ->setForwardAgent(true)
    ->set('deploy_path', '~/rotativ-box.sandbox-skrea.fr')
    ->set('branch', 'main')
    ->set('writable_dirs', [])
    ->set('git_ssh_command', 'ssh')
    ->set(
        'composer_options',
        '--verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader'
    )
    ->set('keep_releases', 2)
    ->set('bin/composer', function () {
        // Sur O2Switch - composer 2.0 par défaut -> ce chemin = composer 2.2.

        return '/opt/cpanel/composer/bin/composer.rpmnew';
    })
    ->set(
        'env',
        [
            'APP_ENV' => 'dev',
        ]
    )
;

// Tasks :
desc('Builds Assets');
task(
    'assets:build',
    function () {
        runLocally('npm run build');
    }
);

desc('Migrate Database');
task(
    'database:migrate',
    function () {
        run('{{bin/php}} {{bin/console}} doctrine:schema:update -f');
    }
);

desc('Uploads Assets');
task(
    'assets:upload',
    function () {
        upload('public/build', '{{release_path}}/public');
    }
);

// Hooks
before('deploy:prepare', 'assets:build');
before('deploy:symlink', 'database:migrate');
before('deploy:symlink', 'assets:upload');
after('deploy:failed', 'deploy:unlock');