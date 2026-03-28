<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DependanceActivite extends Model
{
    protected $table = 'dependances_activites';

    protected $fillable = [
        'activite_id', 'activite_predecesseur_id', 'type_dependance', 'delai_jours',
    ];

    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class, 'activite_id');
    }

    public function predecesseur(): BelongsTo
    {
        return $this->belongsTo(Activite::class, 'activite_predecesseur_id');
    }
}
