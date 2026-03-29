<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class GeneratedReport extends Model
{
    protected $fillable = [
        'uuid',
        'report_definition_id',
        'user_id',
        'papa_id',
        'titre',
        'format',
        'statut',
        'file_disk',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'filters',
        'contexte',
        'generated_at',
        'expires_at',
        'failed_at',
        'last_downloaded_at',
        'error_message',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $report): void {
            if (! $report->uuid) {
                $report->uuid = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'contexte' => 'array',
            'generated_at' => 'datetime',
            'expires_at' => 'datetime',
            'failed_at' => 'datetime',
            'last_downloaded_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(ReportDefinition::class, 'report_definition_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function papa(): BelongsTo
    {
        return $this->belongsTo(Papa::class);
    }

    public function downloadLogs(): HasMany
    {
        return $this->hasMany(ReportDownloadLog::class);
    }

    public function canBeDownloaded(): bool
    {
        return $this->statut === 'generated' && filled($this->file_path);
    }

    public function canRetry(): bool
    {
        return $this->statut === 'failed' && $this->report_definition_id !== null;
    }

    public function formatBadgeClass(): string
    {
        return match ($this->format) {
            'pdf' => 'bg-red-100 text-red-700',
            'xlsx' => 'bg-emerald-100 text-emerald-700',
            'csv' => 'bg-sky-100 text-sky-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->statut) {
            'generated' => 'bg-emerald-100 text-emerald-700',
            'queued', 'processing' => 'bg-amber-100 text-amber-700',
            'failed' => 'bg-rose-100 text-rose-700',
            'expired', 'archived' => 'bg-slate-100 text-slate-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function downloadUrl(): ?string
    {
        if (! Route::has('reports.library.download')) {
            return null;
        }

        return route('reports.library.download', $this);
    }
}
