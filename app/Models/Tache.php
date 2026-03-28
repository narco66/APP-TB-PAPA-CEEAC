<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tache extends Model
{
    use SoftDeletes;

    protected $table = 'taches';

    protected $fillable = [
        'activite_id', 'parent_tache_id', 'code', 'libelle', 'description', 'ordre',
        'date_debut_prevue', 'date_fin_prevue', 'date_debut_reelle', 'date_fin_reelle',
        'statut', 'taux_realisation', 'assignee_id', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_debut_prevue' => 'date',
            'date_fin_prevue' => 'date',
            'date_debut_reelle' => 'date',
            'date_fin_reelle' => 'date',
            'taux_realisation' => 'decimal:2',
        ];
    }

    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class);
    }

    public function parentTache(): BelongsTo
    {
        return $this->belongsTo(Tache::class, 'parent_tache_id');
    }

    public function soustaches(): HasMany
    {
        return $this->hasMany(Tache::class, 'parent_tache_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function scopeRacines($query)
    {
        return $query->whereNull('parent_tache_id');
    }

    public function estEnRetard(): bool
    {
        return !in_array($this->statut, ['terminee', 'abandonnee'])
            && $this->date_fin_prevue
            && $this->date_fin_prevue->isPast();
    }

    public function couleurStatut(): string
    {
        return match($this->statut) {
            'a_faire'    => 'gray',
            'en_cours'   => 'blue',
            'en_revue'   => 'yellow',
            'terminee'   => 'green',
            'bloquee'    => 'orange',
            'abandonnee' => 'red',
            default      => 'gray',
        };
    }
}
