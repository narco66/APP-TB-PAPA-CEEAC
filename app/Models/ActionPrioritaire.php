<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ActionPrioritaire extends Model
{
    use HasFactory;
    use SoftDeletes, LogsActivity;

    protected $table = 'actions_prioritaires';

    protected $fillable = [
        'papa_id', 'departement_id', 'code', 'libelle', 'description',
        'qualification', 'ordre', 'priorite', 'statut',
        'taux_realisation', 'created_by', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'taux_realisation' => 'decimal:2',
            'ordre' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    // ─── Relations ──────────────────────────────────────────────

    public function papa(): BelongsTo
    {
        return $this->belongsTo(Papa::class);
    }

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function objectifsImmediat(): HasMany
    {
        return $this->hasMany(ObjectifImmediats::class);
    }

    public function indicateurs(): HasMany
    {
        return $this->hasMany(Indicateur::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(BudgetPapa::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeTechnique($query)
    {
        return $query->where('qualification', 'technique');
    }

    public function scopeAppui($query)
    {
        return $query->where('qualification', 'appui');
    }

    public function scopePourDepartement($query, int $departementId)
    {
        return $query->where('departement_id', $departementId);
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function couleurPriorite(): string
    {
        return match($this->priorite) {
            'critique' => 'red',
            'haute'    => 'orange',
            'normale'  => 'blue',
            'basse'    => 'gray',
            default    => 'gray',
        };
    }

    public function couleurStatut(): string
    {
        return match($this->statut) {
            'planifie'   => 'gray',
            'en_cours'   => 'blue',
            'suspendu'   => 'yellow',
            'termine'    => 'green',
            'abandonne'  => 'red',
            default      => 'gray',
        };
    }

    public function estEditable(): bool
    {
        return $this->papa && $this->papa->estEditable();
    }
}
