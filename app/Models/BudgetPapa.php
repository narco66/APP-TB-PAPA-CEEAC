<?php

namespace App\Models;

use App\Services\Security\UserScopeResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BudgetPapa extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'budgets_papa';

    protected $fillable = [
        'papa_id', 'action_prioritaire_id', 'activite_id', 'partenaire_id',
        'source_financement', 'libelle_ligne', 'annee_budgetaire', 'devise',
        'montant_prevu', 'montant_engage', 'montant_decaisse', 'montant_solde',
        'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'montant_prevu' => 'decimal:2',
            'montant_engage' => 'decimal:2',
            'montant_decaisse' => 'decimal:2',
            'montant_solde' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Budget {$this->libelle_ligne} : {$eventName}");
    }

    // ─── Relations ──────────────────────────────────────────────

    public function papa(): BelongsTo
    {
        return $this->belongsTo(Papa::class);
    }

    public function actionPrioritaire(): BelongsTo
    {
        return $this->belongsTo(ActionPrioritaire::class);
    }

    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class);
    }

    public function partenaire(): BelongsTo
    {
        return $this->belongsTo(Partenaire::class);
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function engagements(): HasMany
    {
        return $this->hasMany(EngagementFinancier::class, 'budget_papa_id');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        $scope = app(UserScopeResolver::class)->resolve($user);

        if ($scope->isGlobal) {
            return $query;
        }

        return $query->where(function (Builder $budgetQuery) use ($user) {
            $budgetQuery
                ->whereHas('actionPrioritaire', fn (Builder $actionQuery) => $actionQuery->visibleTo($user))
                ->orWhereHas('activite', fn (Builder $activiteQuery) => $activiteQuery->visibleTo($user));
        });
    }

    public function canBeAccessedBy(User $user): bool
    {
        return static::query()->whereKey($this->id)->visibleTo($user)->exists();
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function tauxEngagement(): float
    {
        if ($this->montant_prevu <= 0) return 0;
        return round((float)$this->montant_engage / (float)$this->montant_prevu * 100, 2);
    }

    public function tauxDecaissement(): float
    {
        if ($this->montant_prevu <= 0) return 0;
        return round((float)$this->montant_decaisse / (float)$this->montant_prevu * 100, 2);
    }

    public function libelleSource(): string
    {
        return match($this->source_financement) {
            'budget_ceeac'                  => 'Budget CEEAC',
            'contribution_etat_membre'      => 'Contribution État membre',
            'partenaire_technique_financier' => 'PTF',
            'fonds_propres'                 => 'Fonds propres',
            'autre'                         => 'Autre',
            default                         => ucfirst($this->source_financement),
        };
    }
}
