<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Activite extends Model
{
    use HasFactory;
    use SoftDeletes, LogsActivity;

    protected $table = 'activites';

    protected $fillable = [
        'resultat_attendu_id', 'direction_id', 'service_id',
        'code', 'libelle', 'description', 'ordre',
        'date_debut_prevue', 'date_fin_prevue', 'date_debut_reelle', 'date_fin_reelle',
        'statut', 'taux_realisation',
        'responsable_id', 'point_focal_id',
        'budget_prevu', 'budget_engage', 'budget_consomme', 'devise',
        'priorite', 'est_jalon', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_debut_prevue' => 'date',
            'date_fin_prevue' => 'date',
            'date_debut_reelle' => 'date',
            'date_fin_reelle' => 'date',
            'taux_realisation' => 'decimal:2',
            'budget_prevu' => 'decimal:2',
            'budget_engage' => 'decimal:2',
            'budget_consomme' => 'decimal:2',
            'est_jalon' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    // ─── Relations ──────────────────────────────────────────────

    public function resultatAttendu(): BelongsTo
    {
        return $this->belongsTo(ResultatAttendu::class);
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function pointFocal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'point_focal_id');
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function taches(): HasMany
    {
        return $this->hasMany(Tache::class);
    }

    public function jalons(): HasMany
    {
        return $this->hasMany(Jalon::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(BudgetPapa::class);
    }

    public function engagements(): HasMany
    {
        return $this->hasMany(EngagementFinancier::class);
    }

    public function alertes(): MorphMany
    {
        return $this->morphMany(Alerte::class, 'alertable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function predecesseurs(): HasMany
    {
        return $this->hasMany(DependanceActivite::class, 'activite_id');
    }

    public function successeurs(): HasMany
    {
        return $this->hasMany(DependanceActivite::class, 'activite_predecesseur_id');
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeEnRetard($query)
    {
        return $query->where('statut', '!=', 'terminee')
                     ->where('statut', '!=', 'abandonnee')
                     ->where('date_fin_prevue', '<', now()->toDateString());
    }

    public function scopePourDirection($query, int $directionId)
    {
        return $query->where('direction_id', $directionId);
    }

    // ─── Helpers métier ─────────────────────────────────────────

    public function estEnRetard(): bool
    {
        return !in_array($this->statut, ['terminee', 'abandonnee'])
            && $this->date_fin_prevue
            && $this->date_fin_prevue->isPast();
    }

    public function dureePrevisionnelleDays(): ?int
    {
        if (!$this->date_debut_prevue || !$this->date_fin_prevue) {
            return null;
        }
        return $this->date_debut_prevue->diffInDays($this->date_fin_prevue);
    }

    public function resteAEngager(): float
    {
        return max(0, (float)$this->budget_prevu - (float)$this->budget_engage);
    }

    public function couleurStatut(): string
    {
        return match($this->statut) {
            'non_demarree' => 'gray',
            'planifiee'    => 'blue',
            'en_cours'     => 'indigo',
            'suspendue'    => 'yellow',
            'terminee'     => 'green',
            'abandonnee'   => 'red',
            default        => 'gray',
        };
    }

    public function couleurPriorite(): string
    {
        return match($this->priorite) {
            'critique' => 'red',
            'haute'    => 'orange',
            'normale'  => 'blue',
            'basse'    => 'gray',
            default    => 'gray',
        };
    }

    // Données pour Gantt
    public function toGanttTask(): array
    {
        return [
            'id'       => $this->id,
            'text'     => $this->libelle,
            'start_date' => $this->date_debut_prevue?->format('d-m-Y'),
            'end_date'   => $this->date_fin_prevue?->format('d-m-Y'),
            'progress'   => (float) $this->taux_realisation / 100,
            'color'      => $this->estEnRetard() ? '#ef4444' : '#6366f1',
        ];
    }
}
