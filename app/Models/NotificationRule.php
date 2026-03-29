<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationRule extends Model
{
    use HasFactory;

    protected $table = 'notification_rules';

    protected $fillable = [
        'code',
        'libelle',
        'event_type',
        'canal',
        'role_cible',
        'permission_cible',
        'delai_minutes',
        'escalade',
        'template_sujet',
        'template_message',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'delai_minutes' => 'integer',
            'escalade' => 'boolean',
            'actif' => 'boolean',
        ];
    }
}
