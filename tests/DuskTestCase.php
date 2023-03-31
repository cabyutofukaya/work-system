<?php

namespace Tests;

use Artisan;
use Dotenv\Dotenv;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        if (! static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    public static function basePath($path = '')
    {
        return __DIR__ . '/../' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public static function setUpBeforeClass(): void
    {
        // ブラウザテスト用の環境変数差分をオーバーロードする
        $dotenv = Dotenv::createUnsafeMutable(self::basePath(), ".env.dusk.overload");
        $dotenv->load();

        // キャッシュして新しい環境変数を元にコンフィグを再生成
        // config:clear では環境変数から再生成されない
        // Artisan::call は利用できない
        exec(sprintf('cd "%s"; php artisan config:cache', self::basePath()));

        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        // テスト終了後にテスト用のコンフィグキャッシュを削除
        exec(sprintf('cd "%s"; php artisan config:clear', self::basePath()));

        parent::tearDownAfterClass();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments(collect([
            '--window-size=1920,1080',
            '--lang=ja_JP',
        ])->unless($this->hasHeadlessDisabled(), function ($items) {
            return $items->merge([
            '--disable-gpu',
            '--headless',
        ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
            ChromeOptions::CAPABILITY, $options
        )
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     *
     * @return bool
     */
    protected function hasHeadlessDisabled()
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
               isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }
}
