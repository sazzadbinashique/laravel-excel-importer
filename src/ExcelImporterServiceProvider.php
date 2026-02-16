<?php

namespace SazzadBinAshique\LaravelExcelImporter;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use SazzadBinAshique\LaravelExcelImporter\Console\Commands\CleanupImportFiles;

class ExcelImporterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/excel-importer.php',
            'excel-importer'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'excel-importer');

        // Register Livewire component
        Livewire::component('excel-importer', \SazzadBinAshique\LaravelExcelImporter\Livewire\ExcelImporter::class);

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupImportFiles::class,
            ]);

            // Publish config
            $this->publishes([
                __DIR__.'/../config/excel-importer.php' => config_path('excel-importer.php'),
            ], 'excel-importer-config');

            // Publish migrations
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'excel-importer-migrations');

            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/excel-importer'),
            ], 'excel-importer-views');

            // Publish assets
            $this->publishes([
                __DIR__.'/../public' => public_path('vendor/excel-importer'),
            ], 'excel-importer-assets');
        }
    }
}
