<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ValeurIndicateur extends Model
{
    use LogsActivity;

    protected $table = 'valeurs_indicateurs';

    protected $fillable = [
        'indicateur_id', 'periode_type', 'periode_libelle',
        'annee', 'mois', 'trimestre', 'semestre',
        'valeur_realisee', 'valeur_cible_periode', 'taux_realisation', 'tendance',
        'commentaire', 'analyse_ecart',
        'statut_validation', 'saisi_par', 'valide_par', 'valide_le', 'motif_rejet',
    ];

    protected function casts(): array
    {
        return [
            'valeur_realisee' => 'decimal:4',
            'valeur_cible_periode' => 'decimal:4',
            'taux_realisation' => 'decimal:2',
            'valide_le' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $e) => "ValeurIndicateur ({$this->periode_libelle}) : {$e}");
    }

    // ─── Relations ──────────────────────────────────────────────

    public function indicateur(): BelongsTo
    {
        return $this->belongsTo(Indicateur::class);
    }

    public function saisePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'saisi_par');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function estValide(): bool
    {
        return $this->statut_validation === 'valide';
    }

    public function estBrouillon(): bool
    {
        return $this->statut_validation === 'brouillon';
    }

    public function couleurStatut(): string
    {
        return match($this->statut_validation) {
            'brouillon' => 'gray',
            'soumis'    => 'blue',
            'valide'    => 'green',
            'rejete'    => 'red',
            default     => 'gray',
        };
    }
}
