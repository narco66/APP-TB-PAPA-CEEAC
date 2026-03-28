<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Alerte extends Model
{
    use HasFactory;
    protected $table = 'alertes';

    protected $fillable = [
        'papa_id', 'alertable_type', 'alertable_id',
        'type_alerte', 'niveau', 'titre', 'message', 'statut',
        'destinataire_id', 'direction_id',
        'escaladee', 'escaladee_vers_id', 'escaladee_le',
        'lue_le', 'traitee_par', 'traitee_le', 'resolution', 'auto_generee',
    ];

    protected function casts(): array
    {
        return [
            'escaladee_le' => 'datetime',
            'lue_le' => 'datetime',
            'traitee_le' => 'datetime',
            'escaladee' => 'boolean',
            'auto_generee' => 'boolean',
        ];
    }

    // ─── Relations ──────────────────────────────────────────────

    public function alertable(): MorphTo
    {
        return $this->morphTo();
    }

    public function papa(): BelongsTo
    {
        return $this->belongsTo(Papa::class);
    }

    public function destinataire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function traitePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traitee_par');
    }

    public function actionsCorrectives(): HasMany
    {
        return $this->hasMany(ActionCorrective::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeNouvelle($query)
    {
        return $query->where('statut', 'nouvelle');
    }

    public function scopeCritique($query)
    {
        return $query->where('niveau', 'critique');
    }

    public function scopePourUser($query, int $userId)
    {
        return $query->where('destinataire_id', $userId);
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function estNouvelle(): bool
    {
        return $this->statut === 'nouvelle';
    }

    public function couleurNiveau(): string
    {
        return match($this->niveau) {
            'info'      => 'blue',
            'attention' => 'yellow',
            'critique'  => 'red',
            default     => 'gray',
        };
    }

    public function iconeNiveau(): string
    {
        return match($this->niveau) {
            'info'      => '💬',
            'attention' => '⚠️',
            'critique'  => '🔴',
            default     => 'ℹ️',
        };
    }
}
