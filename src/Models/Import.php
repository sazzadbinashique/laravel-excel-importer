<?php

namespace SazzadBinAshique\LaravelExcelImporter\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $table = 'imports';

    protected $fillable = [
        'file_path',
        'original_filename',
        'import_type',
        'status',
        'total_rows',
        'processed_rows',
        'successful_rows',
        'failed_rows',
        'error_message',
        'error_path',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'successful_rows' => 'integer',
        'failed_rows' => 'integer',
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
        return in_array($this->status, ['completed', 'failed'], true);
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
