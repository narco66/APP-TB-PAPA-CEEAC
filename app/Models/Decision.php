<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Decision extends Model
{
    use HasFactory;

    protected $table = 'decisions';

    protected $fillable = [
        'reference',
        'titre',
        'description',
        'type_decision',
        'niveau_decision',
        'statut',
        'date_decision',
        'papa_id',
        'action_prioritaire_id',
        'activite_id',
        'budget_papa_id',
        'prise_par',
        'validee_par',
        'impact_budgetaire',
        'impact_calendrier_jours',
        'mise_en_oeuvre_obligatoire',
        'date_effet',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'date_decision' => 'date',
            'impact_budgetaire' => 'decimal:2',
            'impact_calendrier_jours' => 'integer',
            'mise_en_oeuvre_obligatoire' => 'boolean',
            'date_effet' => 'date',
            'metadata' => 'array',
        ];
    }

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

    public function budgetPapa(): BelongsTo
    {
        return $this->belongsTo(BudgetPapa::class);
    }

    public function prisePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prise_par');
    }

    public function valideePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validee_par');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(DecisionAttachment::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->resolveVisibilityScope()->isGlobal) {
            return $query;
        }

        return $query->where(function (Builder $decisionQuery) use ($user) {
            $decisionQuery
                ->whereHas('papa', fn (Builder $papaQuery) => $papaQuery->visibleTo($user))
                ->orWhereHas('actionPrioritaire', fn (Builder $actionQuery) => $actionQuery->visibleTo($user))
                ->orWhereHas('activite', fn (Builder $activiteQuery) => $activiteQuery->visibleTo($user))
                ->orWhereHas('budgetPapa', fn (Builder $budgetQuery) => $budgetQuery->visibleTo($user))
                ->orWhere('prise_par', $user->id)
                ->orWhere('validee_par', $user->id);
        });
    }

    public function canBeAccessedBy(User $user): bool
    {
        return static::query()->whereKey($this->id)->visibleTo($user)->exists();
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
