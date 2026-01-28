<?php

namespace MetaFramework\Inputable\Support;

use Illuminate\Console\Command;

class InstallPublishablesCommand extends Command
{
    protected $signature = 'mfw-inputable:install {--force : Overwrite existing publishable resources}';

    protected $description = 'Copy Metaframework Inputable publishable translations and assets into the application';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        $results = PublishablesInstaller::install($force);

        foreach ($results['copied'] as $path) {
            $this->info("Copied: {$path}");
        }

        foreach ($results['skipped'] as $path) {
            $this->comment("Skipped (exists): {$path}");
        }

        foreach ($results['failed'] as $failure) {
            $this->error("Failed: {$failure['destination']} ({$failure['error']})");
        }

        if (! empty($results['failed'])) {
            return self::FAILURE;
        }

        $this->info('Metaframework Inputable publishable resources installed successfully.');

        return self::SUCCESS;
    }
}
