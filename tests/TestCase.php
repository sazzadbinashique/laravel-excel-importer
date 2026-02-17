<?php

namespace SazzadBinAshique\LaravelExcelImporter\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use SazzadBinAshique\LaravelExcelImporter\ExcelImporterServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ExcelImporterServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('filesystems.default', 'local');
        $app['config']->set('excel-importer.storage_disk', 'local');
        $app['config']->set('excel-importer.import_types', [
            'users' => \SazzadBinAshique\LaravelExcelImporter\Tests\Fixtures\UsersImport::class,
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/Fixtures');
    }
}
