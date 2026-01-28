<?php

declare(strict_types=1);

namespace MetaFramework\Inputable\Tests\Feature;

use MetaFramework\Inputable\Tests\TestCase;

class InstallPublishablesCommandTest extends TestCase
{
    public function test_install_command_reports_success(): void
    {
        $this->artisan('mfw-inputable:install')
            ->expectsOutputToContain('Metaframework Inputable publishable resources installed successfully.')
            ->assertExitCode(0);
    }
}
