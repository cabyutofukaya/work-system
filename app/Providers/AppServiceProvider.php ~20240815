<?php

namespace App\Providers;

use App\Http\Middleware\RequestLogger;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(RequestLogger::class);

        // local・テスト・CI環境のみマクロを含むDuskブラウザテストのサービスプロバイダを登録する
        if ($this->app->environment('local', 'testing', 'ci')) {
            $this->app->register(DuskServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // サブディレクトリにシステムをデプロイした場合でもtelescopeを動作させる
        // c.f. [Fix: front-end not working when Laravel is installed in a subdirectory by Zerquix18 · Pull Request #281 · laravel/telescope](https://github.com/laravel/telescope/pull/281)
        $url_path = parse_url(config('app.url'), PHP_URL_PATH);
        $url_path = preg_replace("/^\/+/", "", $url_path ?? "");
        $url_path = preg_replace("/\/+$/", "", $url_path ?? "");
        if ($url_path) {
            View::composer(['telescope::layout'], function ($view) use ($url_path) {
                $view->with('telescopeScriptVariables', [
                    'path' => $url_path . '/telescope',
                    'timezone' => config('app.timezone'),
                    'recording' => !cache('telescope:pause-recording'),
                ]);
            });
        }
    }
}
