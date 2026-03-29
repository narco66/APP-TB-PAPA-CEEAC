<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Rapport extends Model
{
    use HasFactory;
    use SoftDeletes, LogsActivity;

    protected $table = 'rapports';

    protected $fillable = [
        'papa_id', 'direction_id', 'departement_id',
        'titre', 'type_rapport', 'periode_couverte', 'annee', 'numero_periode',
        'taux_execution_physique', 'taux_execution_financiere',
        'faits_saillants', 'difficultes_rencontrees', 'recommandations', 'perspectives',
        'statut', 'redige_par', 'valide_par', 'valide_le', 'publie_le',
    ];

    protected function casts(): array
    {
        return [
            'taux_execution_physique' => 'decimal:2',
            'taux_execution_financiere' => 'decimal:2',
            'valide_le' => 'datetime',
            'publie_le' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Rapport {$this->titre} : {$eventName}");
    }

    public function papa(): BelongsTo
    {
        return $this->belongsTo(Papa::class);
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    public function redigePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redige_par');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if (
            $user->can('rapport.valider')
            || $user->can('rapport.publier')
            || $user->can('admin.utilisateurs')
        ) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($user) {
            $builder->where('redige_par', $user->id);

            if ($user->direction_id) {
                $builder->orWhere('direction_id', $user->direction_id);
            }
        });
    }

    public function canBeAccessedBy(User $user): bool
    {
        if (
            $user->can('rapport.valider')
            || $user->can('rapport.publier')
            || $user->can('admin.utilisateurs')
        ) {
            return true;
        }

        if ($this->redige_par === $user->id) {
            return true;
        }

        return $user->direction_id !== null
            && $this->direction_id !== null
            && $this->direction_id === $user->direction_id;
    }

    public function couleurStatut(): string
    {
        return match($this->statut) {
            'brouillon' => 'gray',
            'soumis'    => 'blue',
            'valide'    => 'green',
            'publie'    => 'indigo',
            default     => 'gray',
        };
    }
}
