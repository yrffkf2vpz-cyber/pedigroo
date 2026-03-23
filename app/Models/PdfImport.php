<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class PdfImport extends Model
{
    protected $table = 'pedroo_pdf_imports';

    protected $fillable = [
        'user_id',
        'type',
        'source',
        'file_path',
        'status',
        'stats',
        'log',
    ];

    protected $casts = [
        'stats' => 'array',
        'log'   => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markRunning(): void
    {
        $this->update(['status' => 'running']);
    }

    public function markDone(array $stats): void
    {
        $this->update([
            'status' => 'done',
            'stats'  => $stats,
        ]);
    }

    public function markFailed(string $message, ?array $context = null): void
    {
        $log = $this->log ?? [];
        $log[] = [
            'time'    => now()->toIso8601String(),
            'message' => $message,
            'context' => $context,
        ];

        $this->update([
            'status' => 'failed',
            'log'    => $log,
        ]);
    }

    public function appendLog(string $message, ?array $context = null): void
    {
        $log = $this->log ?? [];
        $log[] = [
            'time'    => now()->toIso8601String(),
            'message' => $message,
            'context' => $context,
        ];

        $this->update(['log' => $log]);
    }
}
