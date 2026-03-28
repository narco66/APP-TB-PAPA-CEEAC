<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rapport extends Model
{
    use SoftDeletes;

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
