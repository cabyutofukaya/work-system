<?php

return [
    // Inertiaルートテンプレートにメタタグとして出力してフロントで取得可能とする値
    'front' => [
        'sentry_dsn' => env('SENTRY_DSN'),
    ],
    // ドメイン
    'app_domain' => 'localhost:8888',
];
