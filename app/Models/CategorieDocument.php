<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieDocument extends Model
{
    protected $table = 'categories_documents';

    protected $fillable = ['code', 'libelle', 'description', 'icone', 'actif'];

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'categorie_id');
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}
