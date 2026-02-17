<?php

namespace SazzadBinAshique\LaravelExcelImporter\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use SazzadBinAshique\LaravelExcelImporter\Models\Import;

class CleanupImportFiles extends Command
{
    protected $signature = 'excel-importer:cleanup {--disk=} {--days=}';

    protected $description = 'Delete old import and error files';

    public function handle(): int
    {
        $disk = $this->option('disk') ?: config('excel-importer.storage_disk', 'local');
        $days = (int) ($this->option('days') ?: config('excel-importer.retention_days', 1));
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Starting cleanup of files older than {$days} day(s)...");
        $this->newLine();

        $importFilesDeleted = $this->cleanupFiles($disk, config('excel-importer.import_path', 'imports'), $cutoffDate);
        $errorFilesDeleted = $this->cleanupFiles($disk, config('excel-importer.error_path', 'errors'), $cutoffDate);
        $recordsDeleted = $this->cleanupImportRecords($disk, $cutoffDate);

        $this->newLine();
        $this->info('Cleanup Summary:');
        $this->table(
            ['Type', 'Count'],
            [
                ['Import Files', $importFilesDeleted],
                ['Error Files', $errorFilesDeleted],
                ['Database Records', $recordsDeleted],
            ]
        );

        $this->info('Cleanup completed successfully.');

        return self::SUCCESS;
    }

    protected function cleanupFiles(string $disk, string $path, Carbon $cutoffDate): int
    {
        $count = 0;
        $storage = Storage::disk($disk);
        $path = trim($path, '/');

        foreach ($storage->allFiles($path) as $file) {
            $lastModified = Carbon::createFromTimestamp($storage->lastModified($file));

            if ($lastModified->lt($cutoffDate)) {
                $storage->delete($file);
                $count++;
            }
        }

        return $count;
    }

    protected function cleanupImportRecords(string $disk, Carbon $cutoffDate): int
    {
        $storage = Storage::disk($disk);

        $imports = Import::query()
            ->where('created_at', '<', $cutoffDate)
            ->where('status', '!=', 'processing')
            ->get();

        $count = $imports->count();

        foreach ($imports as $import) {
            if ($import->file_path && $storage->exists($import->file_path)) {
                $storage->delete($import->file_path);
            }

            if ($import->error_path && $storage->exists($import->error_path)) {
                $storage->delete($import->error_path);
            }

            $import->delete();
        }

        return $count;
    }
}
