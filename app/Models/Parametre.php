<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parametre extends Model
{
    protected $table = 'parametres';

    protected $fillable = [
        'cle', 'groupe', 'type', 'valeur', 'valeur_defaut',
        'libelle', 'description', 'est_systeme', 'est_sensible', 'modifie_par',
    ];

    protected function casts(): array
    {
        return [
            'est_systeme'  => 'boolean',
            'est_sensible' => 'boolean',
        ];
    }

    public function modificateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modifie_par');
    }

    public function valeurCastee(): mixed
    {
        $v = $this->valeur ?? $this->valeur_defaut;
        return match ($this->type) {
            'boolean' => filter_var($v, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $v,
            'json'    => json_decode($v, true),
            default   => $v,
        };
    }
}
