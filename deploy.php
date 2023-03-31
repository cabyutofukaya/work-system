<?php
namespace Deployer;

require 'recipe/laravel.php';

import('deployer/artisan-backup-run.php');
import('deployer/artisan-migrate-check.php');
import('deployer/deploy-confirm.php');

// Config

// Project name
set('application', getenv('APP_NAME', 'Laravel'));

// Project repository
set('repository', 'ssh://git@github.com/cellnavicorp/grouptube-biz.git');

// PHPバージョンをデフォルトから変更する場合は指定
//set('bin/php', '/usr/local/php/7.4/bin/php');

// さくらレンタルサーバへ追加インストールした場合のGitのパスを指定
// デフォルトでインストールされてる古いバージョン(2.7)ではデプロイ時にエラーとなるため
//set('bin/git', '~/local/bin/git');

// accept-newフラグに対応していない古いOpenSSHへの接続を行う場合に設定
// c.f. https://deployer.org/docs/7.x/recipe/deploy/update_code#git_ssh_command
set('git_ssh_command', 'ssh');

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);

// Hosts

// Hosts production
host('production')
    ->setHostname('grouptube.biz')
    ->set('labels', ['stage' => 'production'])
    ->set('remote_user', 'shin-gt')
    ->set('deploy_path', '~/grouptube-biz/production')
    ->set('branch', 'master');

// Hosts staging
host('staging')
    ->setHostname('grouptube.biz')
    ->set('labels', ['stage' => 'staging'])
    ->set('remote_user', 'shin-gt')
    ->set('deploy_path', '~/grouptube-biz/staging')
    ->set('branch', 'release-staging');

// Hosts develop
host('develop')
    ->setHostname('grouptube.biz')
    ->set('labels', ['stage' => 'develop'])
    ->set('remote_user', 'shin-gt')
    ->set('deploy_path', '~/grouptube-biz/develop')
    ->set('branch', 'release-develop');

// Tasks

//task('build', function () {
//    cd('{{release_path}}');
//    run('npm run build');
//});


// Hook

/**
 * build: Code and composer vendors are ready but nothing is built. (after deploy:vendors)
 */

//

/**
 * ready: Deployment is done but not live yet (before symlink)
 * 列挙した逆順に実行される
 */

// マイグレーション実行
before('deploy:symlink', 'artisan:migrate');

// マイグレーションチェック
before('deploy:symlink', 'artisan:migrate:check');

// DBバックアップ
before('deploy:symlink', 'artisan:backup:run');

/**
 * done: Deployment is done and live (after deploy:cleanup)
 */

//

/**
 * success: Deployment succeeded (after deploy:success)
 * */

//

/**
 * fail: Deployment failed (after deploy:failed)
 */

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
