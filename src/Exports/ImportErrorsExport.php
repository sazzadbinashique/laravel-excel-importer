<?php

namespace SazzadBinAshique\LaravelExcelImporter\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SazzadBinAshique\LaravelExcelImporter\Models\Import;

class ImportErrorsExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    private $failures;

    private Import $import;

    public function __construct($failures, Import $import)
    {
        $this->failures = $failures;
        $this->import = $import;
    }

    public function collection(): Collection
    {
        $rows = new Collection();

        foreach ($this->failures as $failure) {
            $rows->push([
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => implode(', ', $failure->errors()),
                'values' => implode(', ', $failure->values()),
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Row Number',
            'Column',
            'Error Message',
            'Value',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EF4444'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Import Errors';
    }
}
