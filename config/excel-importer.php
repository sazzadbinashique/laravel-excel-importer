<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The disk where import and error files will be stored.
    | Default: 'local' (storage/app)
    |
    */
    'storage_disk' => env('EXCEL_IMPORTER_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Import Directory
    |--------------------------------------------------------------------------
    |
    | Directory path where uploaded import files will be stored.
    |
    */
    'import_path' => 'imports',

    /*
    |--------------------------------------------------------------------------
    | Error Directory
    |--------------------------------------------------------------------------
    |
    | Directory path where error report files will be stored.
    |
    */
    'error_path' => 'errors',

    /*
    |--------------------------------------------------------------------------
    | File Retention Days
    |--------------------------------------------------------------------------
    |
    | Number of days to keep import and error files before cleanup.
    |
    */
    'retention_days' => env('EXCEL_IMPORTER_RETENTION_DAYS', 1),

    /*
    |--------------------------------------------------------------------------
    | Cleanup Schedule Time
    |--------------------------------------------------------------------------
    |
    | Time of day to run the cleanup job (24-hour format).
    | Default: 12:00 (noon)
    |
    */
    'cleanup_time' => env('EXCEL_IMPORTER_CLEANUP_TIME', '12:00'),

    /*
    |--------------------------------------------------------------------------
    | Batch Size
    |--------------------------------------------------------------------------
    |
    | Number of rows to process in each batch during import.
    |
    */
    'batch_size' => env('EXCEL_IMPORTER_BATCH_SIZE', 100),

    /*
    |--------------------------------------------------------------------------
    | Chunk Size
    |--------------------------------------------------------------------------
    |
    | Number of rows to read at a time from the Excel file.
    |
    */
    'chunk_size' => env('EXCEL_IMPORTER_CHUNK_SIZE', 100),

    /*
    |--------------------------------------------------------------------------
    | Max File Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size allowed for upload (in kilobytes).
    |
    */
    'max_file_size' => env('EXCEL_IMPORTER_MAX_FILE_SIZE', 10240), // 10MB

    /*
    |--------------------------------------------------------------------------
    | Preview Rows
    |--------------------------------------------------------------------------
    |
    | Number of rows to show in the preview before import.
    |
    */
    'preview_rows' => 10,

    /*
    |--------------------------------------------------------------------------
    | Progress Update Interval
    |--------------------------------------------------------------------------
    |
    | Interval in seconds for progress updates (Livewire polling).
    |
    */
    'progress_interval' => 2, // seconds

    /*
    |--------------------------------------------------------------------------
    | Dashboard Routes
    |--------------------------------------------------------------------------
    |
    | Configure the dashboard route prefix and middleware.
    |
    */
    'route_prefix' => env('EXCEL_IMPORTER_ROUTE_PREFIX', 'excel-importer'),

    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Recent Imports
    |--------------------------------------------------------------------------
    |
    | Number of recent imports to show on the dashboard.
    |
    */
    'recent_imports' => 5,

    /*
    |--------------------------------------------------------------------------
    | Import Types
    |--------------------------------------------------------------------------
    |
    | Define your import types here. Each type maps to an import class.
    | You can add as many import types as you need.
    |
    | Example:
    | 'users' => \App\Imports\UsersImport::class,
    | 'products' => \App\Imports\ProductsImport::class,
    |
    */
    'import_types' => [
        'users' => \App\Imports\UsersImport::class,
        'products' => \App\Imports\ProductsImport::class,
        // Add more import types here...
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Connection
    |--------------------------------------------------------------------------
    |
    | Queue connection to use for processing imports.
    | Set to null to use the default queue connection.
    |
    */
    'queue_connection' => env('EXCEL_IMPORTER_QUEUE', null),

    /*
    |--------------------------------------------------------------------------
    | Queue Name
    |--------------------------------------------------------------------------
    |
    | Queue name to use for processing imports.
    |
    */
    'queue_name' => env('EXCEL_IMPORTER_QUEUE_NAME', 'default'),

];
