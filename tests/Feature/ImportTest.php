<?php

namespace SazzadBinAshique\LaravelExcelImporter\Tests\Feature;

use Illuminate\Support\Facades\Storage;
use SazzadBinAshique\LaravelExcelImporter\Jobs\ProcessImportJob;
use SazzadBinAshique\LaravelExcelImporter\Models\Import;
use SazzadBinAshique\LaravelExcelImporter\Tests\TestCase;

class ImportTest extends TestCase
{
    public function test_import_job_processes_csv_and_creates_models(): void
    {
        Storage::fake('local');

        $source = base_path('public/sample-import.csv');
        $path = 'imports/sample-import.csv';

        Storage::disk('local')->put($path, file_get_contents($source));

        $import = Import::create([
            'file_path' => $path,
            'original_filename' => 'sample-import.csv',
            'import_type' => 'users',
            'status' => 'pending',
            'total_rows' => 2,
        ]);

        $job = new ProcessImportJob(
            $import,
            \SazzadBinAshique\LaravelExcelImporter\Tests\Fixtures\UsersImport::class,
            'local'
        );

        $job->handle();

        $import->refresh();

        $this->assertSame('completed', $import->status);
        $this->assertSame(2, $import->successful_rows);
        $this->assertSame(0, $import->failed_rows);
        $this->assertDatabaseCount('users', 2);
    }

    public function test_livewire_view_is_loadable(): void
    {
        $view = $this->app['view']->make('excel-importer::livewire.excel-importer')->render();

        $this->assertStringContainsString('Excel Importer', $view);
    }
}
