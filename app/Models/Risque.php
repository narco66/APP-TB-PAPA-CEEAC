<?php

namespace App\Models;

use App\Services\Security\UserScopeResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Risque extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'risques';

    protected $fillable = [
        'entite_type', 'entite_id', 'papa_id',
        'code', 'libelle', 'description', 'categorie',
        'probabilite', 'impact', 'score_risque', 'niveau_risque', 'statut',
        'mesures_mitigation', 'plan_contingence',
        'responsable_id', 'date_echeance_traitement', 'date_derniere_revue',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_echeance_traitement' => 'date',
            'date_derniere_revue' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Risque {$this->code} : {$eventName}");
    }

    public function papa(): BelongsTo
    {
        return $this->belongsTo(Papa::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actionsCorrectives(): HasMany
    {
        return $this->hasMany(ActionCorrective::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        $scope = app(UserScopeResolver::class)->resolve($user);

        if ($scope->isGlobal) {
            return $query;
        }

        return $query->where(function (Builder $risqueQuery) use ($user) {
            $risqueQuery
                ->whereHas('papa', fn (Builder $papaQuery) => $papaQuery->visibleTo($user))
                ->orWhere('responsable_id', $user->id);
        });
    }

    public function canBeAccessedBy(User $user): bool
    {
        return static::query()->whereKey($this->id)->visibleTo($user)->exists();
    }

    public function calculerScore(): int
    {
        $probabiliteScore = match($this->probabilite) {
            'tres_faible' => 1, 'faible' => 2, 'moyenne' => 3,
            'elevee' => 4, 'tres_elevee' => 5, default => 1,
        };
        $impactScore = match($this->impact) {
            'negligeable' => 1, 'mineur' => 2, 'modere' => 3,
            'majeur' => 4, 'catastrophique' => 5, default => 1,
        };
        return $probabiliteScore * $impactScore;
    }

    public function calculerNiveau(): string
    {
        $score = $this->score_risque ?: $this->calculerScore();
        return match(true) {
            $score >= 15  => 'rouge',
            $score >= 8   => 'orange',
            $score >= 3   => 'jaune',
            default       => 'vert',
        };
    }

    public function couleurNiveau(): string
    {
        return match($this->niveau_risque) {
            'rouge'  => 'red',
            'orange' => 'orange',
            'jaune'  => 'yellow',
            'vert'   => 'green',
            default  => 'gray',
        };
    }
}
