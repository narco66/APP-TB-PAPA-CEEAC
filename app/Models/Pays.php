<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pays extends Model
{
    protected $table = 'pays';

    protected $fillable = ['nom', 'nom_court', 'code_iso', 'drapeau', 'actif'];

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function libelleAffichage(): string
    {
        return $this->nom_court ?? $this->nom;
    }
}
