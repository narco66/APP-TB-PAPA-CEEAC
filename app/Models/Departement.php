<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'departements';

    protected $fillable = [
        'code',
        'libelle',
        'libelle_court',
        'type',
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

    public function directions(): HasMany
    {
        return $this->hasMany(Direction::class);
    }

    public function actionsPrioritaires(): HasMany
    {
        return $this->hasMany(ActionPrioritaire::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeTechnique($query)
    {
        return $query->where('type', 'technique');
    }

    public function scopeAppui($query)
    {
        return $query->where('type', 'appui');
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function estTechnique(): bool
    {
        return $this->type === 'technique';
    }

    public function estAppui(): bool
    {
        return $this->type === 'appui';
    }

    public function libelleAffichage(): string
    {
        return $this->libelle_court ?? $this->libelle;
    }
}
