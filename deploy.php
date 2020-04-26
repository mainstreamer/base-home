<?php

namespace Deployer;

require 'recipe/symfony4.php';

// Configuration
set('repository', 'git@base.github.com:mainstreamer/base-home.git');
set('git_tty', false); // [Optional] Allocate tty for git on first deployment
add('shared_files', []);
add('shared_dirs', ['public/uploads']);
add('writable_dirs', ['public/uploads']);
set('allow_anonymous_stats', false);
set('http_user', 'www-data');
set('env', [
    'APP_ENV' => 'prod',
    'DATABASE_URL' => 'mysql://webapp:12345678@127.0.0.1:3306/base',
    'MAILER_URL' => 'smtp://smtp.sendgrid.net:587?encryption=tls&username=apikey&password=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;',
    'LAST_UPDATE' => 'sei4as'
]);

// Hosts
host('178.62.215.91')
    ->user('root')
    ->set('branch', 'master')
    ->stage('production')
    ->set('deploy_path', '/var/www/base');

// Tasks
desc('Restart PHP-FPM service');
task('php-fpm:restart', function () {

// The user must have rights for restart service
// /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo service php7.2-fpm restart');
});
after('deploy:symlink', 'php-fpm:restart');
// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');
