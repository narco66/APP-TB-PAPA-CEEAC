<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionCorrective extends Model
{
    protected $table = 'actions_correctives';

    protected $fillable = [
        'alerte_id', 'risque_id', 'papa_id', 'code', 'libelle', 'description',
        'date_echeance', 'priorite', 'statut',
        'responsable_id', 'date_realisation_effective', 'resultat_obtenu', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_echeance' => 'date',
            'date_realisation_effective' => 'date',
        ];
    }

    public function alerte(): BelongsTo
    {
        return $this->belongsTo(Alerte::class);
    }

    public function risque(): BelongsTo
    {
        return $this->belongsTo(Risque::class);
    }

    public function papa(): BelongsTo
    {
        return $this->belongsTo(Papa::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function couleurStatut(): string
    {
        return match($this->statut) {
            'planifiee' => 'blue',
            'en_cours'  => 'indigo',
            'terminee'  => 'green',
            'annulee'   => 'red',
            default     => 'gray',
        };
    }
}
