<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $table = 'services';

    protected $fillable = [
        'direction_id',
        'code',
        'libelle',
        'libelle_court',
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

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function activites(): HasMany
    {
        return $this->hasMany(Activite::class);
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function libelleAffichage(): string
    {
        return $this->libelle_court ?? $this->libelle;
    }
}
