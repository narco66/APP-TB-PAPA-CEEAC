<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ValidationWorkflow extends Model
{
    protected $table = 'validations_workflow';

    protected $fillable = [
        'validable_type', 'validable_id', 'papa_id',
        'etape', 'action', 'acteur_id',
        'commentaire', 'motif_rejet',
        'statut_avant', 'statut_apres',
    ];

    public function validable(): MorphTo
    {
        return $this->morphTo();
    }

    public function papa(): BelongsTo
    {
        return $this->belongsTo(Papa::class);
    }

    public function acteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acteur_id');
    }

    public function libelleAction(): string
    {
        return match($this->action) {
            'soumis'             => 'Soumis',
            'approuve'           => 'Approuvé',
            'rejete'             => 'Rejeté',
            'demande_correction' => 'Correction demandée',
            'information'        => 'Information',
            default              => ucfirst($this->action),
        };
    }

    public function couleurAction(): string
    {
        return match($this->action) {
            'soumis'             => 'blue',
            'approuve'           => 'green',
            'rejete'             => 'red',
            'demande_correction' => 'yellow',
            'information'        => 'gray',
            default              => 'gray',
        };
    }
}
