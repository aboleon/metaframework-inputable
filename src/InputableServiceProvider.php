<?php

namespace MetaFramework\Inputable;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use MetaFramework\Inputable\Support\InstallPublishablesCommand;

class InputableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'mfw-inputable');
        $appLangPath = lang_path();

        $this->loadTranslationsFrom(__DIR__ . '/../publishable/lang', 'mfw-inputable');

        if (is_dir($appLangPath)) {
            $this->loadTranslationsFrom($appLangPath, 'mfw-inputable');
        }

        Blade::componentNamespace('MetaFramework\\Inputable\\Components', 'mfw-inputable');

        $this->publishes([
            __DIR__ . '/../publishable/lang' => lang_path(),
        ], 'mfw-inputable-translations');

        $this->publishes([
            __DIR__ . '/../publishable/assets' => public_path('vendor/mfw-inputable'),
        ], 'mfw-inputable-assets');

        $this->publishes([
            __DIR__ . '/../publishable/stubs' => base_path('stubs/vendor/mfw-inputable'),
        ], 'mfw-inputable-stubs');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallPublishablesCommand::class,
            ]);
        }
    }
}
