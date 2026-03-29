<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Indicateur extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'indicateurs';

    protected $fillable = [
        'resultat_attendu_id', 'objectif_immediat_id', 'action_prioritaire_id',
        'code', 'libelle', 'definition', 'unite_mesure', 'type_indicateur',
        'valeur_baseline', 'valeur_cible_annuelle',
        'methode_calcul', 'frequence_collecte', 'source_donnees', 'outil_collecte',
        'responsable_id', 'direction_id',
        'seuil_alerte_rouge', 'seuil_alerte_orange', 'seuil_alerte_vert',
        'taux_realisation_courant', 'tendance', 'actif', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'valeur_baseline' => 'decimal:4',
            'valeur_cible_annuelle' => 'decimal:4',
            'seuil_alerte_rouge' => 'decimal:2',
            'seuil_alerte_orange' => 'decimal:2',
            'seuil_alerte_vert' => 'decimal:2',
            'taux_realisation_courant' => 'decimal:2',
            'actif' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Indicateur {$this->code} : {$eventName}");
    }

    // ─── Relations ──────────────────────────────────────────────

    public function resultatAttendu(): BelongsTo
    {
        return $this->belongsTo(ResultatAttendu::class);
    }

    public function objectifImmediats(): BelongsTo
    {
        return $this->belongsTo(ObjectifImmediats::class, 'objectif_immediat_id');
    }

    public function actionPrioritaire(): BelongsTo
    {
        return $this->belongsTo(ActionPrioritaire::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function valeurs(): HasMany
    {
        return $this->hasMany(ValeurIndicateur::class);
    }

    public function dernierValeur()
    {
        return $this->hasOne(ValeurIndicateur::class)->latestOfMany('created_at');
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeQuantitatif($query)
    {
        return $query->where('type_indicateur', 'quantitatif');
    }

    // ─── Helpers métier ─────────────────────────────────────────

    public function niveauAlerte(): string
    {
        $taux = (float) $this->taux_realisation_courant;

        if ($this->seuil_alerte_rouge && $taux <= $this->seuil_alerte_rouge) {
            return 'rouge';
        }
        if ($this->seuil_alerte_orange && $taux <= $this->seuil_alerte_orange) {
            return 'orange';
        }
        if ($this->seuil_alerte_vert && $taux >= $this->seuil_alerte_vert) {
            return 'vert';
        }

        return 'neutre';
    }

    public function couleurTendance(): string
    {
        return match($this->tendance) {
            'hausse' => 'green',
            'stable' => 'blue',
            'baisse' => 'red',
            default  => 'gray',
        };
    }

    public function iconesTendance(): string
    {
        return match($this->tendance) {
            'hausse' => '↑',
            'stable' => '→',
            'baisse' => '↓',
            default  => '–',
        };
    }
}
