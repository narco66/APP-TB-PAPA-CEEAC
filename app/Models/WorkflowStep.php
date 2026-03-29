<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $table = 'workflow_steps';

    protected $fillable = [
        'workflow_definition_id',
        'code',
        'libelle',
        'description',
        'ordre',
        'role_requis',
        'permission_requise',
        'validation_multiple',
        'nb_validateurs_min',
        'est_etape_initiale',
        'est_etape_finale',
        'delai_jours',
        'escalade_apres_jours',
    ];

    protected function casts(): array
    {
        return [
            'ordre' => 'integer',
            'validation_multiple' => 'boolean',
            'nb_validateurs_min' => 'integer',
            'est_etape_initiale' => 'boolean',
            'est_etape_finale' => 'boolean',
            'delai_jours' => 'integer',
            'escalade_apres_jours' => 'integer',
        ];
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(WorkflowDefinition::class, 'workflow_definition_id');
    }

    public function instancesCourantes(): HasMany
    {
        return $this->hasMany(WorkflowInstance::class, 'etape_courante_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(WorkflowAction::class);
    }
}
