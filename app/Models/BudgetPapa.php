<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetPapa extends Model
{
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
