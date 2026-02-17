<?php

namespace SazzadBinAshique\LaravelExcelImporter\Tests\Unit;

use SazzadBinAshique\LaravelExcelImporter\Console\Commands\CleanupImportFiles;
use SazzadBinAshique\LaravelExcelImporter\Tests\TestCase;

class CleanupTest extends TestCase
{
    public function test_cleanup_command_is_resolvable(): void
    {
        $command = $this->app->make(CleanupImportFiles::class);

        $this->assertInstanceOf(CleanupImportFiles::class, $command);
    }
}
