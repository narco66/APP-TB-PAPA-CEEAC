<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Route;

class NotificationApp extends Model
{
    protected $table = 'notifications_app';

    protected $fillable = [
        'user_id', 'type', 'titre', 'message', 'lien', 'icone', 'niveau',
        'notifiable_type', 'notifiable_id', 'lue_le',
    ];

    protected function casts(): array
    {
        return [
            'lue_le' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function marquerCommeLue(): void
    {
        if (!$this->lue_le) {
            $this->update(['lue_le' => now()]);
        }
    }

    public function estLue(): bool
    {
        return $this->lue_le !== null;
    }

    public function couleurNiveau(): string
    {
        return match($this->niveau) {
            'info'      => 'blue',
            'succes'    => 'green',
            'attention' => 'yellow',
            'erreur'    => 'red',
            default     => 'gray',
        };
    }

    public function scopeNonLues(Builder $query): Builder
    {
        return $query->whereNull('lue_le');
    }

    public function scopeLues(Builder $query): Builder
    {
        return $query->whereNotNull('lue_le');
    }

    public function sourceLabel(): string
    {
        if ($this->notifiable_type && $this->notifiable_id) {
            return class_basename((string) $this->notifiable_type) . ' #' . $this->notifiable_id;
        }

        return $this->titre;
    }

    public function sourceUrl(): ?string
    {
        if (!empty($this->lien)) {
            return $this->lien;
        }

        if (!$this->notifiable_type || !$this->notifiable_id) {
            return null;
        }

        return match ($this->notifiable_type) {
            WorkflowInstance::class => Route::has('workflows.show')
                ? route('workflows.show', $this->notifiable_id)
                : null,
            Decision::class => Route::has('decisions.show')
                ? route('decisions.show', $this->notifiable_id)
                : null,
            Papa::class => Route::has('papas.show')
                ? route('papas.show', $this->notifiable_id)
                : null,
            default => null,
        };
    }
}
