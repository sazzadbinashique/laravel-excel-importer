<?php

namespace SazzadBinAshique\LaravelExcelImporter\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use SazzadBinAshique\LaravelExcelImporter\Jobs\ProcessImportJob;
use SazzadBinAshique\LaravelExcelImporter\Models\Import;

class ExcelImporter extends Component
{
    use WithFileUploads;

    public $file;
    public ?Import $import = null;
    public array $previewData = [];
    public bool $showPreview = false;
    public bool $importing = false;
    public string $importType = '';
    public ?string $importClass = null;
    public array $importTypes = [];

    protected function rules(): array
    {
        $maxSize = (int) config('excel-importer.max_file_size', 10240);

        return [
            'file' => "required|file|mimes:xlsx,xls,csv|max:{$maxSize}",
            'importType' => 'required|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'file.required' => 'Please select a file to upload.',
            'file.mimes' => 'File must be an Excel file (xlsx, xls, or csv).',
            'file.max' => 'File size must not exceed the configured limit.',
            'importType.required' => 'Import type is required.',
        ];
    }

    public function mount(?string $type = null): void
    {
        $this->importTypes = config('excel-importer.import_types', []);
        if (empty($this->importTypes)) {
            $this->importType = '';
            $this->importClass = null;
            return;
        }

        $this->importType = $type ?: (string) array_key_first($this->importTypes);
        $this->importClass = $this->getImportClass($this->importType);
    }

    public function updatedImportType(): void
    {
        if ($this->importType === '') {
            $this->importClass = null;
            $this->reset(['file', 'previewData', 'showPreview', 'importing', 'import']);
            return;
        }

        $this->importClass = $this->getImportClass($this->importType);
        $this->reset(['file', 'previewData', 'showPreview', 'importing', 'import']);
    }

    protected function getImportClass(string $type): string
    {
        if (!isset($this->importTypes[$type])) {
            throw new \RuntimeException("Import type '{$type}' is not configured.");
        }

        return $this->importTypes[$type];
    }

    public function updatedFile(): void
    {
        $this->validate();
        $this->loadPreview();
    }

    public function loadPreview(): void
    {
        try {
            $path = $this->file->getRealPath();

            $tempImport = new Import();
            $importInstance = new $this->importClass($tempImport);
            $data = Excel::toArray($importInstance, $path);

            if (empty($data) || empty($data[0])) {
                $this->addError('file', 'The file is empty or invalid.');
                return;
            }

            $rows = $data[0];
            $headers = array_shift($rows);
            $previewRows = (int) config('excel-importer.preview_rows', 10);

            $this->previewData = [
                'headers' => $headers,
                'rows' => array_slice($rows, 0, $previewRows),
                'total' => count($data[0]) - 1,
            ];

            $this->showPreview = true;
        } catch (\Throwable $e) {
            $this->addError('file', 'Error reading file: '.$e->getMessage());
        }
    }

    public function startImport(): void
    {
        $this->validate();

        if (!$this->importClass) {
            $this->addError('importType', 'Please configure at least one import type.');
            return;
        }

        try {
            $disk = $this->getStorageDisk();
            $importPath = trim((string) config('excel-importer.import_path', 'imports'), '/');

            Storage::disk($disk)->makeDirectory($importPath);

            $filename = time().'_'.$this->file->getClientOriginalName();
            $path = $this->file->storeAs($importPath, $filename, $disk);

            $this->import = Import::create([
                'file_path' => $path,
                'original_filename' => $this->file->getClientOriginalName(),
                'import_type' => $this->importType,
                'status' => 'pending',
                'total_rows' => $this->previewData['total'] ?? 0,
            ]);

            ProcessImportJob::dispatch($this->import, $this->importClass, $disk);

            $this->importing = true;
            $this->showPreview = false;
        } catch (\Throwable $e) {
            $this->addError('file', 'Error starting import: '.$e->getMessage());
        }
    }

    public function checkProgress(): void
    {
        if ($this->import) {
            $this->import->refresh();

            if ($this->import->isCompleted()) {
                $this->importing = false;
            }
        }
    }

    public function cancelImport(): void
    {
        $this->reset(['file', 'previewData', 'showPreview', 'importing', 'import']);
    }

    public function downloadErrors()
    {
        if (!$this->import || !$this->import->error_path) {
            return;
        }

        $disk = $this->getStorageDisk();

        if (!Storage::disk($disk)->exists($this->import->error_path)) {
            $this->addError('file', 'Error file not found.');
            return;
        }

        $filename = 'import_errors_'.$this->import->id.'.xlsx';

        return Storage::disk($disk)->download($this->import->error_path, $filename);
    }

    public function clearImport(): void
    {
        if (!$this->import) {
            return;
        }

        $disk = $this->getStorageDisk();
        $storage = Storage::disk($disk);

        if ($this->import->file_path && $storage->exists($this->import->file_path)) {
            $storage->delete($this->import->file_path);
        }

        if ($this->import->error_path && $storage->exists($this->import->error_path)) {
            $storage->delete($this->import->error_path);
        }

        $this->import->delete();

        $this->reset(['file', 'previewData', 'showPreview', 'importing', 'import']);
    }

    protected function getStorageDisk(): string
    {
        return (string) config('excel-importer.storage_disk', 'local');
    }

    public function render()
    {
        $recentImports = Import::latest()->take((int) config('excel-importer.recent_imports', 5))->get();

        return view('excel-importer::livewire.excel-importer', [
            'recentImports' => $recentImports,
        ]);
    }
}
