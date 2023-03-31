<?php

namespace Deployer;

/**
 * デプロイ時に確認を行う (本番環境のみ)
 * hook:start
 */
desc('Execute artisan deploy:confirm');
task('deploy:confirm', function () {
    if (!askConfirmation(sprintf('デプロイを開始します。よろしいですか？ APP_ENV:[%s]', input()->getArgument('stage')), false)) {
        writeln('デプロイを中止しました');
        exit();
    }
})->select('stage=production');
