<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResultatAttendu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resultats_attendus';

    protected $fillable = [
        'objectif_immediat_id', 'code', 'libelle', 'description',
        'type_resultat', 'ordre', 'statut', 'taux_atteinte',
        'preuve_requise', 'type_preuve_attendue',
        'responsable_id', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'taux_atteinte' => 'decimal:2',
            'preuve_requise' => 'boolean',
        ];
    }

    // ─── Relations ──────────────────────────────────────────────

    public function objectifImmediats(): BelongsTo
    {
        return $this->belongsTo(ObjectifImmediats::class, 'objectif_immediat_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function activites(): HasMany
    {
        return $this->hasMany(Activite::class);
    }

    public function indicateurs(): HasMany
    {
        return $this->hasMany(Indicateur::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeOutput($query)
    {
        return $query->where('type_resultat', 'output');
    }

    public function scopeOutcome($query)
    {
        return $query->where('type_resultat', 'outcome');
    }

    // ─── Helpers ────────────────────────────────────────────────

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

    public function libelleTypeResultat(): string
    {
        return match($this->type_resultat) {
            'output'  => 'Extrant',
            'outcome' => 'Effet',
            'impact'  => 'Impact',
            default   => ucfirst($this->type_resultat),
        };
    }

    public function preuveManquante(): bool
    {
        return $this->preuve_requise && $this->documents()->count() === 0;
    }
}
