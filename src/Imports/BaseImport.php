<?php

namespace SazzadBinAshique\LaravelExcelImporter\Imports;

use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterBatch;
use SazzadBinAshique\LaravelExcelImporter\Models\Import;

abstract class BaseImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnError,
    SkipsOnFailure,
    WithEvents
{
    use SkipsErrors, SkipsFailures;

    protected Import $import;
    protected int $processedRows = 0;
    protected int $successfulRows = 0;

    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    abstract public function model(array $row);

    abstract public function rules(): array;

    public function customValidationMessages(): array
    {
        return [];
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function batchSize(): int
    {
        return (int) config('excel-importer.batch_size', 100);
    }

    public function chunkSize(): int
    {
        return (int) config('excel-importer.chunk_size', 100);
    }

    public function registerEvents(): array
    {
        return [
            AfterBatch::class => function (AfterBatch $event) {
                if (!$this->import->exists) {
                    return;
                }

                $batchSize = $event->getConcernable()->batchSize();
                $currentFailures = $this->failures()->count();

                $this->processedRows += $batchSize;
                if ($this->import->total_rows > 0) {
                    $this->processedRows = min($this->processedRows, $this->import->total_rows);
                }
                $this->successfulRows = max($this->processedRows - $currentFailures, 0);

                $this->import->update([
                    'processed_rows' => $this->processedRows,
                    'successful_rows' => $this->successfulRows,
                    'failed_rows' => $currentFailures,
                ]);
            },
        ];
    }

    public function getImport(): Import
    {
        return $this->import;
    }
}
