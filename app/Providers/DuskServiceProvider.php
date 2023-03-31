<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // ファイル名を自動で設定してスクリーンショット取得
        Browser::macro('ss', function (): Browser {
            /** @var \Laravel\Dusk\Browser $browser */
            $browser = $this;
            $bt = debug_backtrace();
            $browser->screenshot(pathinfo($bt[2]['file'], PATHINFO_FILENAME) . "-" . $bt[2]['line']);
            return $browser;
        });

        // 通常のclearメソッドではテキストが削除されないVuetifyのinputフィールドの削除メソッド
        // c.f. https://github.com/laravel/dusk/issues/596
        Browser::macro('vuetifyClear', function (string $selector): Browser {
            /** @var \Laravel\Dusk\Browser $browser */
            $browser = $this;
            $browser->keys($selector, ...array_fill(0, strlen(strval($browser->value($selector))), '{backspace}'));
            return $browser;
        });
    }
}
