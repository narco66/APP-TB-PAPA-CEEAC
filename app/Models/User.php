<?php

namespace App\Models;

use Database\Factories\UserFactory;
use App\Support\Security\UserVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'telephone',
        'password',
        'titre',
        'fonction',
        'matricule',
        'avatar',
        'departement_id',
        'direction_id',
        'service_id',
        'scope_level',
        'is_transversal',
        'actif',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'derniere_connexion' => 'datetime',
            'password' => 'hashed',
            'is_transversal' => 'boolean',
            'actif' => 'boolean',
        ];
    }

    // ─── Relations ──────────────────────────────────────────────

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function transversalScopes(): HasMany
    {
        return $this->hasMany(TransversalScope::class);
    }

    public function notificationsApp(): HasMany
    {
        return $this->hasMany(NotificationApp::class);
    }

    public function notificationsNonLues(): HasMany
    {
        return $this->hasMany(NotificationApp::class)->nonLues();
    }

    public function notificationSummary(): array
    {
        $query = $this->notificationsApp();

        return [
            'total' => (clone $query)->count(),
            'non_lues' => (clone $query)->nonLues()->count(),
            'lues' => (clone $query)->lues()->count(),
        ];
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeDeDirection($query, int $directionId)
    {
        return $query->where('direction_id', $directionId);
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function nomComplet(): string
    {
        return trim(($this->prenom ?? '') . ' ' . $this->name);
    }

    public function resolveVisibilityScope(): UserVisibilityScope
    {
        return app(\App\Services\Security\UserScopeResolver::class)->resolve($this);
    }

    public function scopeLabel(): string
    {
        $scope = $this->resolveVisibilityScope();

        if ($scope->isGlobal || $scope->isTransversal) {
            return $scope->label();
        }

        return match ($scope->level) {
            'service' => 'Perimetre de donnees : Service ' . ($this->service?->libelleAffichage() ?? 'non defini'),
            'direction' => 'Perimetre de donnees : Direction ' . ($this->direction?->libelleAffichage() ?? 'non definie'),
            'departement' => 'Perimetre de donnees : Departement ' . ($this->departement?->libelleAffichage() ?? $this->direction?->departement?->libelle ?? 'non defini'),
            default => $scope->label(),
        };
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return asset('images/avatar-default.png');
    }
}
