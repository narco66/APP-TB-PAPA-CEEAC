<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportDefinition extends Model
{
    protected $fillable = [
        'code',
        'libelle',
        'categorie',
        'description',
        'formats',
        'is_async_recommended',
        'is_system',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'formats' => 'array',
            'is_async_recommended' => 'boolean',
            'is_system' => 'boolean',
            'actif' => 'boolean',
        ];
    }

    public function generatedReports(): HasMany
    {
        return $this->hasMany(GeneratedReport::class);
    }
}
