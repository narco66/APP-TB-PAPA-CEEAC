<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WorkflowInstance extends Model
{
    use HasFactory;

    protected $table = 'workflow_instances';

    protected $fillable = [
        'workflow_definition_id',
        'objet_type',
        'objet_id',
        'papa_id',
        'statut',
        'etape_courante_id',
        'demarre_par',
        'date_demarrage',
        'date_cloture',
        'motif_cloture',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'date_demarrage' => 'datetime',
            'date_cloture' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(WorkflowDefinition::class, 'workflow_definition_id');
    }

    public function objet(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'objet_type', 'objet_id');
    }

    public function papa(): BelongsTo
    {
        return $this->belongsTo(Papa::class);
    }

    public function etapeCourante(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'etape_courante_id');
    }

    public function demarrePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'demarre_par');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(WorkflowAction::class);
    }

    public function auditTrailParams(): array
    {
        return [
            'auditable_type' => self::class,
            'auditable_id' => $this->getKey(),
        ];
    }

    public function auditTrailUrl(): string
    {
        return Route::has('admin.audit-events')
            ? route('admin.audit-events', $this->auditTrailParams())
            : '#';
    }
}
