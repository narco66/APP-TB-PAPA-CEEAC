<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partenaire extends Model
{
    use SoftDeletes;

    protected $table = 'partenaires';

    protected $fillable = [
        'code', 'libelle', 'sigle', 'type', 'pays_origine',
        'contact_nom', 'contact_email', 'contact_telephone',
        'site_web', 'notes', 'actif',
    ];

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(BudgetPapa::class);
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function libelleAffichage(): string
    {
        return $this->sigle ?? $this->libelle;
    }
}
