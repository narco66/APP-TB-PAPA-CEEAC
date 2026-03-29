<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionAttachment extends Model
{
    use HasFactory;

    protected $table = 'decision_attachments';

    protected $fillable = [
        'decision_id',
        'document_id',
        'titre',
        'type_piece',
        'version',
        'obligatoire',
        'valide',
        'commentaire_validation',
        'valide_par',
        'valide_le',
    ];

    protected function casts(): array
    {
        return [
            'obligatoire' => 'boolean',
            'valide' => 'boolean',
            'valide_le' => 'datetime',
        ];
    }

    public function decision(): BelongsTo
    {
        return $this->belongsTo(Decision::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }
}
