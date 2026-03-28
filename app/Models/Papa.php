<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Papa extends Model
{
    use HasFactory;
    use SoftDeletes, LogsActivity;

    protected $table = 'papas';

    protected $fillable = [
        'code', 'libelle', 'annee', 'date_debut', 'date_fin',
        'description', 'statut', 'budget_total_prevu', 'devise',
        'taux_execution_physique', 'taux_execution_financiere',
        'created_by', 'validated_by', 'validated_at',
        'archived_by', 'archived_at', 'motif_archivage',
        'clone_de_papa_id', 'notes', 'est_verrouille',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'validated_at' => 'datetime',
            'archived_at' => 'datetime',
            'budget_total_prevu' => 'decimal:2',
            'taux_execution_physique' => 'decimal:2',
            'taux_execution_financiere' => 'decimal:2',
            'est_verrouille' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "PAPA {$this->code} : {$eventName}");
    }

    // ─── Relations ──────────────────────────────────────────────

    public function actionsPrioritaires(): HasMany
    {
        return $this->hasMany(ActionPrioritaire::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(BudgetPapa::class);
    }

    public function alertes(): HasMany
    {
        return $this->hasMany(Alerte::class);
    }

    public function risques(): HasMany
    {
        return $this->hasMany(Risque::class);
    }

    public function rapports(): HasMany
    {
        return $this->hasMany(Rapport::class);
    }

    public function validationsWorkflow(): HasMany
    {
        return $this->hasMany(ValidationWorkflow::class);
    }

    public function cloneDePapa(): BelongsTo
    {
        return $this->belongsTo(Papa::class, 'clone_de_papa_id');
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function archivePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    // ─── Scopes métier ─────────────────────────────────────────

    public function scopeActif($query)
    {
        return $query->whereIn('statut', ['valide', 'en_execution']);
    }

    public function scopeEnExecution($query)
    {
        return $query->where('statut', 'en_execution');
    }

    public function scopeArchive($query)
    {
        return $query->where('statut', 'archive');
    }

    public function scopeParAnnee($query, int $annee)
    {
        return $query->where('annee', $annee);
    }

    // ─── Helpers métier ─────────────────────────────────────────

    public function estArchive(): bool
    {
        return $this->statut === 'archive';
    }

    public function estVerrouille(): bool
    {
        return $this->est_verrouille || $this->estArchive();
    }

    public function estEditable(): bool
    {
        return !$this->estVerrouille() && in_array($this->statut, ['brouillon', 'en_cours', 'en_execution']);
    }

    public function peutEtreValide(): bool
    {
        return $this->statut === 'soumis';
    }

    public function couleurStatut(): string
    {
        return match($this->statut) {
            'brouillon'     => 'gray',
            'soumis'        => 'blue',
            'en_validation' => 'yellow',
            'valide'        => 'green',
            'en_execution'  => 'indigo',
            'cloture'       => 'orange',
            'archive'       => 'red',
            default         => 'gray',
        };
    }

    public function libelleStatut(): string
    {
        return match($this->statut) {
            'brouillon'     => 'Brouillon',
            'soumis'        => 'Soumis',
            'en_validation' => 'En validation',
            'valide'        => 'Validé',
            'en_execution'  => 'En exécution',
            'cloture'       => 'Clôturé',
            'archive'       => 'Archivé',
            default         => ucfirst($this->statut),
        };
    }
}
