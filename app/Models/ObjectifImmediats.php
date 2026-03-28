<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjectifImmediats extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'objectifs_immediats';

    protected $fillable = [
        'action_prioritaire_id', 'code', 'libelle', 'description',
        'ordre', 'statut', 'taux_atteinte', 'responsable_id', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'taux_atteinte' => 'decimal:2',
        ];
    }

    // ─── Relations ──────────────────────────────────────────────

    public function actionPrioritaire(): BelongsTo
    {
        return $this->belongsTo(ActionPrioritaire::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function resultatsAttendus(): HasMany
    {
        return $this->hasMany(ResultatAttendu::class, 'objectif_immediat_id');
    }

    public function indicateurs(): HasMany
    {
        return $this->hasMany(Indicateur::class, 'objectif_immediat_id');
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeAtteint($query)
    {
        return $query->where('statut', 'atteint');
    }

    public function couleurStatut(): string
    {
        return match($this->statut) {
            'planifie'              => 'gray',
            'en_cours'              => 'blue',
            'atteint'               => 'green',
            'partiellement_atteint' => 'yellow',
            'non_atteint'           => 'red',
            default                 => 'gray',
        };
    }
}
