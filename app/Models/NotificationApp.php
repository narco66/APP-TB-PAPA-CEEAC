<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
}
