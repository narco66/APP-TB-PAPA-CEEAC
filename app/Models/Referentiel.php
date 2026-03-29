<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referentiel extends Model
{
    use SoftDeletes;

    protected $table = 'referentiels';

    protected $fillable = [
        'type', 'code', 'libelle', 'libelle_court', 'description',
        'couleur', 'icone', 'ordre', 'actif', 'est_systeme', 'metadata',
        'cree_par', 'modifie_par',
    ];

    protected function casts(): array
    {
        return [
            'actif'       => 'boolean',
            'est_systeme' => 'boolean',
            'metadata'    => 'array',
        ];
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeDeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function modifiePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modifie_par');
    }

    public static function options(string $type): \Illuminate\Support\Collection
    {
        return static::deType($type)->actif()->orderBy('ordre')->orderBy('libelle')->pluck('libelle', 'code');
    }
}
