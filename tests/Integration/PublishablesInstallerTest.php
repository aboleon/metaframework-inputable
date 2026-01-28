<?php

declare(strict_types=1);

namespace MetaFramework\Inputable\Tests\Integration;

use Illuminate\Support\Facades\File;
use MetaFramework\Inputable\Support\PublishablesInstaller;
use MetaFramework\Inputable\Tests\TestCase;

class PublishablesInstallerTest extends TestCase
{
    public function test_install_copies_assets_and_lang_files(): void
    {
        $summary = PublishablesInstaller::install();

        $this->assertFileExists(public_path('vendor/mfw-inputable/components/inputdatemask.js'));
        $this->assertFileExists(lang_path('en/mfw-inputable-messages.php'));
        $this->assertEmpty($summary['failed']);
        $this->assertNotEmpty($summary['copied']);
    }

    public function test_install_skips_non_empty_destination_without_force(): void
    {
        $destination = public_path('vendor/mfw-inputable');
        File::ensureDirectoryExists($destination);
        File::put($destination . DIRECTORY_SEPARATOR . 'marker.txt', 'existing');

        $summary = PublishablesInstaller::install(false);

        $this->assertContains($destination, $summary['skipped']);
        $this->assertFileExists($destination . DIRECTORY_SEPARATOR . 'marker.txt');
    }

    public function test_install_skips_existing_lang_files_without_force(): void
    {
        $langFile = lang_path('en' . DIRECTORY_SEPARATOR . 'mfw-inputable-messages.php');
        File::ensureDirectoryExists(dirname($langFile));
        File::put($langFile, 'custom');

        $summary = PublishablesInstaller::install(false);

        $this->assertContains($langFile, $summary['skipped']);
        $this->assertSame('custom', File::get($langFile));
    }
}
