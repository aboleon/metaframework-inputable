<?php

namespace MetaFramework\Inputable;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class InputableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mfw-input.php', 'mfw-input');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mfw-input');
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'mfw-input');

        Blade::componentNamespace('MetaFramework\\Inputable\\Components', 'mfw-input');

        $this->publishes([
            __DIR__ . '/../config/mfw-input.php' => config_path('mfw-input.php'),
        ], 'mfw-input-config');

        $this->publishes([
            __DIR__ . '/../lang' => lang_path('vendor/mfw-input'),
        ], 'mfw-input-translations');

        $this->publishes([
            __DIR__ . '/../publishable/assets' => public_path('vendor/mfw-input'),
        ], 'mfw-input-assets');

        $this->ensurePublishablesInstalled();
    }

    private function ensurePublishablesInstalled(): void
    {
        $resources = [
            __DIR__ . '/../config/mfw-input.php' => config_path('mfw-input.php'),
            __DIR__ . '/../lang' => lang_path('vendor/mfw-input'),
            __DIR__ . '/../publishable/assets' => public_path('vendor/mfw-input'),
        ];

        foreach ($resources as $source => $destination) {
            if (! File::exists($source)) {
                continue;
            }

            try {
                if (File::isDirectory($source)) {
                    if (File::isDirectory($destination)) {
                        continue;
                    }

                    File::copyDirectory($source, $destination);
                } else {
                    if (File::exists($destination)) {
                        continue;
                    }

                    $directory = dirname($destination);
                    if (! File::isDirectory($directory)) {
                        File::makeDirectory($directory, 0755, true);
                    }

                    File::copy($source, $destination);
                }
            } catch (\Throwable $exception) {
                Log::warning(
                    'Metaframework Inputable: unable to copy publishable resources automatically.',
                    [
                        'source' => $source,
                        'destination' => $destination,
                        'error' => $exception->getMessage(),
                    ]
                );
            }
        }
    }
}
