# Installation Guide - Laravel Excel Importer (Multi-Model)

Complete step-by-step guide to install and configure the Excel Importer package in your Laravel project.

**Author:** Sazzad Bin Ashique  
**Email:** sazzad.sumon35@gmail.com  
**Package:** `sazzadbinashique/laravel-excel-importer`

## Prerequisites

Before installing, ensure you have:

- ✅ Laravel 11.0 or higher
- ✅ PHP 8.2 or higher
- ✅ Composer installed
- ✅ Database configured
- ✅ Queue configured (database, redis, or other)

## Step-by-Step Installation

### 1. Install Package via Composer

```bash
composer require sazzadbinashique/laravel-excel-importer
```

The package will automatically register its service provider via Laravel's package discovery.

### 2. Publish Package Assets

#### Option A: Publish Everything (Recommended)

```bash
php artisan vendor:publish --provider="SazzadBinAshique\LaravelExcelImporter\ExcelImporterServiceProvider"
```

#### Option B: Publish Selectively

```bash
# Publish configuration file
php artisan vendor:publish --tag=excel-importer-config

# Publish migrations
php artisan vendor:publish --tag=excel-importer-migrations

# Publish views (optional - only if customizing)
php artisan vendor:publish --tag=excel-importer-views

# Publish sample files (optional)
php artisan vendor:publish --tag=excel-importer-assets
```

### 3. Run Database Migrations

```bash
php artisan migrate
```

This creates the `imports` table:

```php
Schema::create('imports', function (Blueprint $table) {
    $table->id();
    $table->string('filename');
    $table->string('original_filename');
    $table->string('status')->default('pending');
    $table->integer('total_rows')->default(0);
    $table->integer('processed_rows')->default(0);
    $table->integer('successful_rows')->default(0);
    $table->integer('failed_rows')->default(0);
    $table->text('error_message')->nullable();
    $table->string('error_file')->nullable();
    $table->timestamp('started_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
});
```

### 4. Configure Environment

Add to your `.env` file:

```env
# Queue Configuration (Required)
QUEUE_CONNECTION=database

# Excel Importer Settings (Optional)
EXCEL_IMPORTER_DISK=local
EXCEL_IMPORTER_RETENTION_DAYS=1
EXCEL_IMPORTER_CLEANUP_TIME=12:00
EXCEL_IMPORTER_BATCH_SIZE=100
EXCEL_IMPORTER_CHUNK_SIZE=100
EXCEL_IMPORTER_MAX_FILE_SIZE=10240
EXCEL_IMPORTER_QUEUE=default
```

### 5. Create Storage Directories

The package will auto-create these, but you can create them manually:

```bash
mkdir -p storage/app/imports
mkdir -p storage/app/errors
```

Set permissions:

```bash
# Linux/Mac
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Windows (PowerShell as Admin)
icacls "storage" /grant Users:F /T
```

### 6. Set Up Queue Worker

The package requires a queue worker to process imports in the background.

#### Development

```bash
php artisan queue:work
```

#### Production (Supervisor)

Create `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
stopwaitsecs=3600
```

Reload supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

#### Production (systemd)

Create `/etc/systemd/system/laravel-worker.service`:

```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /path/to/your/project/artisan queue:work database --sleep=3 --tries=3

[Install]
WantedBy=multi-user.target
```

Enable and start:

```bash
sudo systemctl enable laravel-worker
sudo systemctl start laravel-worker
```

### 7. Set Up Task Scheduler

The package includes automatic file cleanup that runs daily.

#### Linux/Mac Crontab

Add to crontab:

```bash
crontab -e
```

Add this line:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

#### Windows Task Scheduler

1. Open Task Scheduler
2. Create Basic Task
3. Name: "Laravel Scheduler"
4. Trigger: Daily, repeat every 1 minute
5. Action: Start a program
6. Program: `C:\php\php.exe`
7. Arguments: `C:\path\to\project\artisan schedule:run`
8. Start in: `C:\path\to\project`

Or use a batch file:

**run-scheduler.bat:**
```batch
@echo off
cd C:\path\to\your\project
php artisan schedule:run
```

Schedule this batch file to run every minute.

### 8. Create Your Import Class

**app/Imports/YourImport.php:**

```php
<?php

namespace App\Imports;

use App\Models\YourModel;
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

class YourImport implements 
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
        return new YourModel([
            'field1' => $row['column1'],
            'field2' => $row['column2'],
            // Add your fields
        ]);
    }

    public function rules(): array
    {
        return [
            'column1' => ['required', 'string'],
            'column2' => ['required', 'email', 'unique:your_table,email'],
            // Add your validation rules
        ];
    }

    public function customValidationMessages()
    {
        return [
            'column1.required' => 'Column 1 is required.',
            'column2.email' => 'Column 2 must be a valid email.',
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

### 9. Create Import Model

**app/Models/Import.php:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $fillable = [
        'filename',
        'original_filename',
        'status',
        'total_rows',
        'processed_rows',
        'successful_rows',
        'failed_rows',
        'error_message',
        'error_file',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_rows === 0) {
            return 0;
        }
        return (int) (($this->processed_rows / $this->total_rows) * 100);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, ['completed', 'failed']);
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed(string $message): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $message,
            'completed_at' => now(),
        ]);
    }
}
```

### 10. Create View

**resources/views/import.blade.php:**

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 py-4">
                <h1 class="text-2xl font-bold text-gray-900">Import Data</h1>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 py-8">
            @livewire('excel-importer')
        </main>
    </div>

    @livewireScripts
</body>
</html>
```

### 11. Add Route

**routes/web.php:**

```php
use Illuminate\Support\Facades\Route;

Route::get('/import', function () {
    return view('import');
})->name('import.index');
```

### 12. Test Installation

1. **Start the queue worker:**
   ```bash
   php artisan queue:work
   ```

2. **Start development server:**
   ```bash
   php artisan serve
   ```

3. **Visit:** `http://localhost:8000/import`

4. **Upload a test file** and verify:
   - File preview works
   - Import processes in background
   - Progress updates in real-time
   - Statistics display correctly
   - Error file download works

## Verification Checklist

- ✅ Package installed via Composer
- ✅ Config file published to `config/excel-importer.php`
- ✅ Migrations run successfully
- ✅ Queue connection configured
- ✅ Queue worker running
- ✅ Scheduler configured (crontab/Task Scheduler)
- ✅ Import model created
- ✅ Import class created
- ✅ Route added
- ✅ View created
- ✅ Storage directories exist
- ✅ Permissions set correctly
- ✅ Test import successful

## Common Issues

### Issue: "Class 'Livewire' not found"

**Solution:**
```bash
composer require livewire/livewire
```

### Issue: "Table 'imports' doesn't exist"

**Solution:**
```bash
php artisan migrate
```

### Issue: Queue not processing

**Solution:**
```bash
# Check queue connection
php artisan queue:work

# Restart queue
php artisan queue:restart
php artisan queue:work
```

### Issue: Files not cleaning up

**Solution:**
```bash
# Test scheduler
php artisan schedule:run

# Run cleanup manually
php artisan imports:cleanup

# Check crontab
crontab -l
```

### Issue: "Storage directory not writable"

**Solution:**
```bash
# Linux/Mac
chmod -R 775 storage
chown -R www-data:www-data storage

# Windows
icacls "storage" /grant Users:F /T
```

## Next Steps

1. **Customize configuration** in `config/excel-importer.php`
2. **Create your import class** with appropriate validation rules
3. **Style the interface** by publishing and editing views
4. **Set up monitoring** for queue jobs
5. **Configure backups** for import data
6. **Test with large files** to verify performance

## Need Help?

- Read the [full documentation](README.md)
- Check [troubleshooting guide](README.md#troubleshooting)
- Submit issues on [GitHub](https://github.com/yourvendor/laravel-excel-importer)

## Production Deployment

### Additional Steps for Production

1. **Optimize configuration:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Set up queue monitoring** (Laravel Horizon recommended)
   ```bash
   composer require laravel/horizon
   php artisan horizon:install
   ```

3. **Enable error logging:**
   ```env
   LOG_CHANNEL=stack
   LOG_LEVEL=error
   ```

4. **Configure file storage:**
   - Use S3 for large-scale deployments
   - Update `EXCEL_IMPORTER_DISK=s3`

5. **Set up monitoring:**
   - Monitor queue length
   - Track import success rates
   - Alert on failures

6. **Regular maintenance:**
   ```bash
   # Weekly: Check storage usage
   php artisan imports:cleanup --days=7
   
   # Monthly: Optimize database
   php artisan db:optimize
   ```

Your Laravel Excel Importer is now ready to use! 🚀
