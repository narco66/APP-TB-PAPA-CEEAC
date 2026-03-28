<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngagementFinancier extends Model
{
    protected $table = 'engagements_financiers';

    protected $fillable = [
        'budget_papa_id', 'activite_id', 'numero_engagement',
        'libelle', 'date_engagement',
        'montant_engage', 'montant_decaisse',
        'fournisseur_beneficiaire', 'statut', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_engagement' => 'date',
            'montant_engage' => 'decimal:2',
            'montant_decaisse' => 'decimal:2',
        ];
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(BudgetPapa::class, 'budget_papa_id');
    }

    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class);
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function resteADecaisser(): float
    {
        return max(0, (float)$this->montant_engage - (float)$this->montant_decaisse);
    }

    public function couleurStatut(): string
    {
        return match($this->statut) {
            'engage'                    => 'blue',
            'partiellement_decaisse'    => 'yellow',
            'totalement_decaisse'       => 'green',
            'annule'                    => 'red',
            default                     => 'gray',
        };
    }
}
