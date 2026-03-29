<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class LibelleMetier extends Model
{
    protected $table = 'libelles_metier';

    protected $fillable = [
        'cle', 'module', 'valeur_defaut', 'valeur_courante',
        'valeur_courte', 'est_systeme', 'traductible', 'locale', 'modifie_par',
    ];

    protected function casts(): array
    {
        return [
            'est_systeme' => 'boolean',
            'traductible' => 'boolean',
        ];
    }

    public function valeurEffective(): string
    {
        return $this->valeur_courante ?? $this->valeur_defaut;
    }

    public function modifiePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modifie_par');
    }

    public static function lire(string $cle, ?string $defaut = null): string
    {
        return Cache::remember("libelle.{$cle}", 3600, function () use ($cle, $defaut) {
            $l = static::where('cle', $cle)->first();
            return $l?->valeurEffective() ?? $defaut ?? $cle;
        });
    }
}
