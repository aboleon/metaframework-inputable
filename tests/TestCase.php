<?php

declare(strict_types=1);

namespace MetaFramework\Inputable\Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use MetaFramework\Inputable\InputableServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected static string $basePath;

    protected string $tempPath;

    public static function applicationBasePath(): string
    {
        return static::$basePath;
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$basePath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . 'mfw-inputable-testbench-'
            . bin2hex(random_bytes(4));

        static::ensureBaseDirectories(static::$basePath);

        if (!class_exists('Str')) {
            class_alias(Str::class, 'Str');
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (isset(static::$basePath)) {
            static::deleteDirectory(static::$basePath);
        }

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        $this->tempPath = static::$basePath;

        parent::setUp();

        $this->setAppPath('usePublicPath', $this->tempPath . DIRECTORY_SEPARATOR . 'public');
        $this->setAppPath('useLangPath', $this->tempPath . DIRECTORY_SEPARATOR . 'lang');
        $this->setAppPath('useStoragePath', $this->tempPath . DIRECTORY_SEPARATOR . 'storage');

        File::ensureDirectoryExists(public_path());
        File::ensureDirectoryExists(lang_path());
        File::ensureDirectoryExists(storage_path('framework/views'));

        $this->app['view']->share('errors', new ViewErrorBag);
        $this->app['translator']->addLines([
            'mfw-inputable-messages.select_option' => 'Select',
            'mfw-inputable-messages.select_date' => 'Choose a date...',
            'mfw-inputable-messages.no_data_provided' => 'No data provided',
            'mfw-inputable-messages.date_placeholder' => 'DD/MM/YYYY',
        ], 'en');
        $this->app['translator']->addLines([
            'messages.date_placeholder' => 'DD/MM/YYYY',
        ], 'en', 'mfw-inputable');
        $this->app['translator']->setLocale('en');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [InputableServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $basePath = static::$basePath;
        $publicPath = $basePath . DIRECTORY_SEPARATOR . 'public';
        $langPath = $basePath . DIRECTORY_SEPARATOR . 'lang';
        $storagePath = $basePath . DIRECTORY_SEPARATOR . 'storage';
        $viewPath = $basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';

        if (method_exists($app, 'usePublicPath')) {
            $app->usePublicPath($publicPath);
        } else {
            $app->instance('path.public', $publicPath);
        }

        if (method_exists($app, 'useLangPath')) {
            $app->useLangPath($langPath);
        } else {
            $app->instance('path.lang', $langPath);
        }

        if (method_exists($app, 'useStoragePath')) {
            $app->useStoragePath($storagePath);
        } else {
            $app->instance('path.storage', $storagePath);
        }

        $app['config']->set('view.compiled', $storagePath . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views');
        $app['config']->set('view.paths', [$viewPath]);
        $app['config']->set('app.locale', 'en');
        $app['config']->set('app.fallback_locale', 'en');
    }

    private function setAppPath(string $method, string $path): void
    {
        if (method_exists($this->app, $method)) {
            $this->app->{$method}($path);

            return;
        }

        if ($method === 'usePublicPath') {
            $this->app->instance('path.public', $path);

            return;
        }

        if ($method === 'useLangPath') {
            $this->app->instance('path.lang', $path);

            return;
        }

        if ($method === 'useStoragePath') {
            $this->app->instance('path.storage', $path);
        }
    }

    private static function ensureBaseDirectories(string $basePath): void
    {
        static::ensureDirectory($basePath . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache');
        static::ensureDirectory($basePath . DIRECTORY_SEPARATOR . 'config');
        static::ensureDirectory($basePath . DIRECTORY_SEPARATOR . 'public');
        static::ensureDirectory($basePath . DIRECTORY_SEPARATOR . 'lang');
        static::ensureDirectory($basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views');
        static::ensureDirectory($basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views');
        static::ensureDirectory($basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'data');
        static::ensureDirectory($basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'sessions');
        static::ensureDirectory($basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs');

        $marker = $basePath . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . '.testbench-default-skeleton';
        if (!file_exists($marker)) {
            file_put_contents($marker, '');
        }
    }

    private static function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    private static function deleteDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }

        rmdir($path);
    }
}
