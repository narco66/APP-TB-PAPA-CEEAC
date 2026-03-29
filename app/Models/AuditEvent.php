<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

class AuditEvent extends Model
{
    use HasFactory;

    protected $table = 'audit_events';

    protected $fillable = [
        'event_uuid',
        'module',
        'event_type',
        'auditable_type',
        'auditable_id',
        'papa_id',
        'acteur_id',
        'action',
        'description',
        'niveau',
        'donnees_avant',
        'donnees_apres',
        'ip_address',
        'user_agent',
        'horodate_evenement',
        'checksum',
    ];

    protected function casts(): array
    {
        return [
            'donnees_avant' => 'array',
            'donnees_apres' => 'array',
            'horodate_evenement' => 'datetime',
        ];
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'auditable_type', 'auditable_id');
    }

    public function papa(): BelongsTo
    {
        return $this->belongsTo(Papa::class);
    }

    public function acteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acteur_id');
    }

    public function scopeForAuditable(Builder $query, Model $model): Builder
    {
        return $query
            ->where('auditable_type', $model::class)
            ->where('auditable_id', $model->getKey());
    }

    public function auditableLabel(): string
    {
        return class_basename((string) $this->auditable_type) . ' #' . $this->auditable_id;
    }

    public function auditableUrl(): ?string
    {
        if (!$this->auditable_id || !$this->auditable_type) {
            return null;
        }

        return match ($this->auditable_type) {
            WorkflowInstance::class => Route::has('workflows.show')
                ? route('workflows.show', $this->auditable_id)
                : null,
            Decision::class => Route::has('decisions.show')
                ? route('decisions.show', $this->auditable_id)
                : null,
            Papa::class => Route::has('papas.show')
                ? route('papas.show', $this->auditable_id)
                : null,
            default => null,
        };
    }
}
