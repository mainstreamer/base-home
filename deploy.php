<?php

namespace Deployer;

require 'recipe/symfony4.php';
// Configuration
set('repository', 'git@base.github.com:mainstreamer/base-home.git');
set('git_tty', false); // [Optional] Allocate tty for git on first deployment
add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);
set('allow_anonymous_stats', false);
set('http_user', 'www-data');
set('env', [
    'APP_ENV' => 'prod',
    'DATABASE_URL' => 'mysql://webapp:12345678@127.0.0.1:3306/base',
]);
// Hosts
//localhost()
//    ->stage('production')
//    ->set('deploy_path', '/var/www/webapp');
host('178.62.215.91')
    ->user('root')
    ->set('branch', 'master')
    ->stage('live')
    ->set('deploy_path', '/var/www/base');
//localhost('production')
//    ->stage('production')
//    ->set('deploy_path', '/var/www/webapp');
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
