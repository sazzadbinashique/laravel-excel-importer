<?php

namespace SazzadBinAshique\LaravelExcelImporter\Jobs;

use SazzadBinAshique\LaravelExcelImporter\Exports\ImportErrorsExport;
use SazzadBinAshique\LaravelExcelImporter\Models\Import;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public function __construct(
        public Import $import,
        public string $importClass,
        public ?string $disk = null
    ) {
        $this->disk = $this->disk ?: config('excel-importer.storage_disk', 'local');

        $connection = config('excel-importer.queue_connection');
        $queue = config('excel-importer.queue_name');

        if ($connection) {
            $this->onConnection($connection);
        }

        if ($queue) {
            $this->onQueue($queue);
        }
    }

    public function handle(): void
    {
        try {
            $this->import->markAsProcessing();

            $importer = new $this->importClass($this->import);

            Excel::import($importer, $this->import->file_path, $this->disk);

            $this->import->refresh();

            $failures = $importer->failures();
            $failedCount = $failures->count();
            $successCount = max($this->import->total_rows - $failedCount, 0);

            $this->import->update([
                'processed_rows' => $this->import->total_rows,
                'successful_rows' => $successCount,
                'failed_rows' => $failedCount,
            ]);

            if ($failedCount > 0) {
                $this->generateErrorReport($failures);
            }

            $this->import->markAsCompleted();
        } catch (\Throwable $e) {
            $this->import->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    protected function generateErrorReport($failures): void
    {
        $errorPath = trim((string) config('excel-importer.error_path', 'errors'), '/');
        $storage = Storage::disk($this->disk);

        $storage->makeDirectory($errorPath);

        $filename = $errorPath.'/import_errors_'.$this->import->id.'_'.time().'.xlsx';

        Excel::store(
            new ImportErrorsExport($failures, $this->import),
            $filename,
            $this->disk
        );

        $this->import->update([
            'error_path' => $filename,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->import->markAsFailed($exception->getMessage());
    }
}
