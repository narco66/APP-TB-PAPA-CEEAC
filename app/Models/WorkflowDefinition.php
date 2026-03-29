<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowDefinition extends Model
{
    use HasFactory;

    protected $table = 'workflow_definitions';

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'module_cible',
        'type_objet',
        'actif',
        'version',
        'cree_par',
        'maj_par',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
            'version' => 'integer',
        ];
    }

    public function steps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class)->orderBy('ordre');
    }

    public function instances(): HasMany
    {
        return $this->hasMany(WorkflowInstance::class);
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function majPar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'maj_par');
    }
}
