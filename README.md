# Laravel Excel Importer Package - Multi-Model System

[![Latest Version](https://img.shields.io/packagist/v/sazzadbinashique/laravel-excel-importer.svg?style=flat-square)](https://packagist.org/packages/sazzadbinashique/laravel-excel-importer)
[![Total Downloads](https://img.shields.io/packagist/dt/sazzadbinashique/laravel-excel-importer.svg?style=flat-square)](https://packagist.org/packages/sazzadbinashique/laravel-excel-importer)
[![License](https://img.shields.io/packagist/l/sazzadbinashique/laravel-excel-importer.svg?style=flat-square)](https://packagist.org/packages/sazzadbinashique/laravel-excel-importer)

A powerful Laravel package for importing **any model** from Excel files with preview, real-time progress tracking, validation, error handling, and automatic cleanup.

**Author:** Sazzad Bin Ashique  
**Email:** sazzad.sumon35@gmail.com

## Features

✅ **Multiple Model Types** - Import any model (Users, Products, or custom)  
✅ **File Upload with Preview** - Preview data before importing  
✅ **Real-time Progress Tracking** - Live updates with Livewire polling  
✅ **Validation** - Built-in validation with detailed error reporting  
✅ **Error Export** - Download Excel files with validation errors  
✅ **Queue Processing** - Background job processing for large files  
✅ **Automatic Cleanup** - Scheduled cleanup of old files  
✅ **Extensible** - Easy to add custom import types  
✅ **Customizable** - Highly configurable via config file  
✅ **Modern UI** - Clean, responsive interface with Tailwind CSS  

## Requirements

- PHP 8.2 or higher
- Laravel 11.0 or higher
- Livewire 3.0 or higher
- Maatwebsite/Laravel-Excel 3.1 or higher

## Installation

### Step 1: Install via Composer

```bash
composer require sazzadbinashique/laravel-excel-importer
```

### Step 2: Publish Assets

```bash
# Publish all assets
php artisan vendor:publish --provider="SazzadBinAshique\LaravelExcelImporter\ExcelImporterServiceProvider"

# Or publish individually
php artisan vendor:publish --tag=excel-importer-config
php artisan vendor:publish --tag=excel-importer-migrations
php artisan vendor:publish --tag=excel-importer-views
php artisan vendor:publish --tag=excel-importer-assets
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

This creates the `imports` table for tracking import jobs.

### Step 4: Configure Queue

Make sure your queue is configured and running:

**.env**
```env
QUEUE_CONNECTION=database
```

**Start the queue worker:**
```bash
php artisan queue:work
```

### Step 5: Set Up Scheduler (Optional)

For automatic file cleanup, add the scheduler to your crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Or on Windows, use Task Scheduler to run:
```bash
php artisan schedule:run
```

## Usage

### Basic Usage

**In your Blade view:**

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Import Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body>
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Import Users</h1>
        
        @livewire('excel-importer')
    </div>
    
    @livewireScripts
</body>
</html>
```

**Add route (routes/web.php):**

```php
use Illuminate\Support\Facades\Route;

Route::get('/import', function () {
    return view('import');
})->name('import.index');
```

### Custom Import Class

Create your own import class extending the base functionality:

**app/Imports/UsersImport.php**

```php
<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Import;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterBatch;
use Illuminate\Support\Facades\Hash;

class UsersImport implements 
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

    protected $import;
    protected $processedRows = 0;
    protected $successfulRows = 0;

    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    public function model(array $row)
    {
        return new User([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email already exists in the database.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }

    public function batchSize(): int
    {
        return config('excel-importer.batch_size', 100);
    }

    public function chunkSize(): int
    {
        return config('excel-importer.chunk_size', 100);
    }

    public function registerEvents(): array
    {
        return [
            AfterBatch::class => function(AfterBatch $event) {
                $batchSize = $event->getConcernable()->batchSize();
                $currentFailures = $this->failures()->count();
                
                $this->processedRows += $batchSize;
                $this->successfulRows = $this->processedRows - $currentFailures;
                
                // Update import progress
                $this->import->update([
                    'processed_rows' => $this->processedRows,
                    'successful_rows' => $this->successfulRows,
                    'failed_rows' => $currentFailures,
                ]);
            },
        ];
    }
}
```

## Configuration

Edit `config/excel-importer.php`:

```php
return [
    // Storage disk for files
    'storage_disk' => 'local',
    
    // File retention period (days)
    'retention_days' => 1,
    
    // Daily cleanup time (24-hour format)
    'cleanup_time' => '12:00',
    
    // Batch and chunk sizes
    'batch_size' => 100,
    'chunk_size' => 100,
    
    // Max file upload size (KB)
    'max_file_size' => 10240, // 10MB
    
    // Progress update interval (seconds)
    'progress_interval' => 2,
    
    // Queue settings
    'queue_connection' => null,
    'queue_name' => 'default',
];
```

## Artisan Commands

### Clean Up Old Files

Manually clean up import and error files:

```bash
# Clean files older than 1 day (default)
php artisan imports:cleanup

# Clean files older than 7 days
php artisan imports:cleanup --days=7

# Clean files older than 30 days
php artisan imports:cleanup --days=30
```

This command removes:
- Import files from `storage/app/imports`
- Error files from `storage/app/errors`
- Associated database records

### Automatic Cleanup

The package automatically schedules cleanup daily at 12:00 PM (configurable). The scheduler runs:

```bash
php artisan imports:cleanup --days=1
```

**Change cleanup time in config:**

```php
'cleanup_time' => '02:00', // Run at 2:00 AM
```

**Or via environment variable:**

```env
EXCEL_IMPORTER_CLEANUP_TIME=02:00
```

## File Format

### Required Excel Format

| name          | email              | password   |
|---------------|-------------------|------------|
| John Doe      | john@example.com  | secret123  |
| Jane Smith    | jane@example.com  | pass1234   |

### Sample Files

The package includes sample file generators:

```bash
# Generate 10,000 row sample file
php generate-sample.php
```

This creates `public/sample-10k.csv` with unique, valid test data.

## Customization

### Customize Livewire Component

Publish and modify the Livewire component:

```bash
php artisan vendor:publish --tag=excel-importer-views
```

Edit: `resources/views/vendor/excel-importer/livewire/excel-importer.blade.php`

### Customize Validation Rules

Update your import class's `rules()` method:

```php
public function rules(): array
{
    return [
        'email' => ['required', 'email', 'unique:users,email'],
        'name' => ['required', 'string', 'max:255'],
        'phone' => ['nullable', 'regex:/^[0-9]{10}$/'],
        'age' => ['nullable', 'integer', 'min:18', 'max:100'],
    ];
}
```

### Customize Error Messages

Override `customValidationMessages()`:

```php
public function customValidationMessages()
{
    return [
        'email.unique' => 'This email is already registered.',
        'phone.regex' => 'Phone number must be 10 digits.',
        'age.min' => 'User must be at least 18 years old.',
    ];
}
```

### Change Batch Size

Adjust performance by changing batch and chunk sizes:

```php
public function batchSize(): int
{
    return 500; // Process 500 rows at a time
}

public function chunkSize(): int
{
    return 500; // Read 500 rows at a time
}
```

**Or in config:**

```env
EXCEL_IMPORTER_BATCH_SIZE=500
EXCEL_IMPORTER_CHUNK_SIZE=500
```

## How It Works

### 1. Upload & Preview
- User uploads Excel/CSV file (up to 10MB)
- System validates file format
- Shows preview of first 10 rows
- Displays total row count

### 2. Background Processing
- User clicks "Start Import"
- File stored in `storage/app/imports`
- Import job dispatched to queue
- Job processes file in batches

### 3. Real-time Progress
- Livewire polls every 2 seconds
- Statistics update after each batch:
  - Total rows
  - Processed rows
  - Successful rows
  - Failed rows
- Progress bar shows completion percentage

### 4. Error Handling
- Invalid rows are collected
- Validation errors tracked per row
- Error report generated as Excel file
- Stored in `storage/app/errors`
- User can download detailed error report

### 5. Automatic Cleanup
- Scheduler runs daily at configured time
- Deletes files older than retention period
- Removes associated database records
- Keeps storage clean automatically

## Troubleshooting

### Queue Not Processing

**Check queue is running:**
```bash
php artisan queue:work
```

**Check queue connection in .env:**
```env
QUEUE_CONNECTION=database
```

### File Upload Errors

**Increase PHP limits in php.ini:**
```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
```

**Check storage permissions:**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Progress Not Updating

**Clear config cache:**
```bash
php artisan config:clear
php artisan cache:clear
```

**Restart queue worker:**
```bash
php artisan queue:restart
php artisan queue:work
```

### Scheduler Not Running

**Verify crontab entry:**
```bash
crontab -l
```

**Test scheduler manually:**
```bash
php artisan schedule:run
```

**Check scheduled tasks:**
```bash
php artisan schedule:list
```

## Testing

Create test files to verify functionality:

**Valid data (sample-import.csv):**
```csv
name,email,password
John Doe,john@example.com,password123
Jane Smith,jane@example.com,secret456
```

**Invalid data (sample-with-errors.csv):**
```csv
name,email,password
,missing@name.com,pass
John,,short
Duplicate,john@example.com,test
```

## Performance Tips

1. **Increase batch size** for faster processing of large files
2. **Use queue workers** with multiple processes: `--queue=default --sleep=3 --tries=3`
3. **Index database columns** used in validation (especially unique checks)
4. **Disable timestamps** temporarily for bulk inserts
5. **Use chunk reading** to manage memory efficiently

## Security

- Files are stored in private storage directory
- File size limited by configuration
- File type validation (only xlsx, xls, csv)
- Queue jobs have timeout protection
- Automatic cleanup prevents storage abuse

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Credits

- Built with [Laravel](https://laravel.com)
- [Livewire](https://livewire.laravel.com) for reactive components
- [Maatwebsite/Laravel-Excel](https://laravel-excel.com) for Excel processing
- [Tailwind CSS](https://tailwindcss.com) for styling

## Support

For issues, questions, or contributions, please visit the [GitHub repository](https://github.com/yourvendor/laravel-excel-importer).
