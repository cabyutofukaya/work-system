<?php

namespace Deployer;

/**
 * 可能であればDBバックアップを行う
 * hook:ready
 */
desc('Execute artisan backup:run --only-db');
task('artisan:backup:run', function () {
    if (!test('[ -s {{release_or_current_path}}/.env ]')) {
        warning("Your .env file is empty! Skipping...");
        return;
    }

    $list = run('{{bin/php}} {{release_or_current_path}}/artisan list');
    if (str_contains($list, 'backup:run')){
        run('{{bin/php}} {{release_or_current_path}}/artisan backup:run --only-db');
    }
});
