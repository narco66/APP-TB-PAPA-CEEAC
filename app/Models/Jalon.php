<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jalon extends Model
{
    protected $table = 'jalons';

    protected $fillable = [
        'activite_id', 'code', 'libelle', 'description',
        'date_prevue', 'date_reelle', 'statut', 'est_critique', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_prevue' => 'date',
            'date_reelle' => 'date',
            'est_critique' => 'boolean',
        ];
    }

    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class);
    }

    public function estManque(): bool
    {
        return $this->statut === 'non_atteint' && $this->date_prevue->isPast();
    }

    public function couleurStatut(): string
    {
        return match($this->statut) {
            'planifie'   => 'blue',
            'atteint'    => 'green',
            'non_atteint' => 'red',
            'reporte'    => 'yellow',
            default      => 'gray',
        };
    }
}
