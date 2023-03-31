<?php

namespace Deployer;

/**
 * マイグレーションが実行される場合は確認を行う (本番環境のみ)
 * hook:ready before artisan:migrate
 */
desc('Execute artisan migrate:check');
task('artisan:migrate:check', function () {
    if (!test('[ -s {{release_or_current_path}}/.env ]')) {
        warning("Your .env file is empty! Skipping...</>");
        return;
    }

    // エラーステータスが返ってこなければ未実行のマイグレーションは存在しないためスキップ
    if (!run("{{bin/php}} {{release_or_current_path}}/artisan migrate:check > /dev/null; echo $?")) {
        return;
    }

    // 未実行のマイグレーションを表示
    // デプロイを停止させないためエラーステータスを無効化して実行
    $output = run('{{bin/php}} {{release_or_current_path}}/artisan migrate:check || true');
    writeln("\n<info>" . $output . "</info>");

    // 確認
    if (!askConfirmation('未実行のマイグレーションが存在します。デプロイを続行してよろしいですか？', false)) {
        throw new \Exception('デプロイを中止しました');
    }
})->select('stage=production');