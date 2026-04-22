<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransversalScope extends Model
{
    protected $fillable = [
        'user_id',
        'scope_type',
        'departement_id',
        'direction_id',
        'service_id',
        'can_view',
        'can_export',
        'can_contribute',
        'starts_at',
        'ends_at',
        'granted_by',
        'motif',
    ];

    protected function casts(): array
    {
        return [
            'can_view' => 'boolean',
            'can_export' => 'boolean',
            'can_contribute' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
