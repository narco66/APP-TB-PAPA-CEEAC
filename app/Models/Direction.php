<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Direction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'directions';

    protected $fillable = [
        'departement_id',
        'code',
        'libelle',
        'libelle_court',
        'type_direction',
        'description',
        'ordre_affichage',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }

    // ─── Relations ──────────────────────────────────────────────

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function activites(): HasMany
    {
        return $this->hasMany(Activite::class);
    }

    public function indicateurs(): HasMany
    {
        return $this->hasMany(Indicateur::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeTechnique($query)
    {
        return $query->where('type_direction', 'technique');
    }

    public function scopeAppui($query)
    {
        return $query->where('type_direction', 'appui');
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function estTechnique(): bool
    {
        return $this->type_direction === 'technique';
    }

    public function estAppui(): bool
    {
        return $this->type_direction === 'appui';
    }

    public function libelleAffichage(): string
    {
        return $this->libelle_court ?? $this->libelle;
    }
}
