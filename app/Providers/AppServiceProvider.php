<?php

namespace App\Providers;

use App\Models\Activite;
use App\Models\Document;
use App\Models\Indicateur;
use App\Models\Papa;
use App\Policies\ActivitePolicy;
use App\Policies\DocumentPolicy;
use App\Policies\IndicateurPolicy;
use App\Policies\PapaPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // ── Policies ────────────────────────────────────────────────────────
        Gate::policy(Papa::class, PapaPolicy::class);
        Gate::policy(Activite::class, ActivitePolicy::class);
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(Indicateur::class, IndicateurPolicy::class);

        // ── Super Admin bypasse toutes les policies ──────────────────────────
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });

        // ── Gates pour les permissions Spatie (authorize()) ──────────────────
        // Permet d'utiliser $this->authorize('papa.voir') dans les contrôleurs
        // sans passer par une policy explicite pour chaque permission
        Gate::define('papa.voir', fn($user) => $user->can('papa.voir'));
        Gate::define('papa.creer', fn($user) => $user->can('papa.creer'));
        Gate::define('papa.modifier', fn($user) => $user->can('papa.modifier'));
        Gate::define('papa.supprimer', fn($user) => $user->can('papa.supprimer'));
        Gate::define('papa.voir_archive', fn($user) => $user->can('papa.voir_archive'));
        Gate::define('activite.voir', fn($user) => $user->can('activite.voir'));
        Gate::define('activite.creer', fn($user) => $user->can('activite.creer'));
        Gate::define('activite.voir_toutes_directions', fn($user) => $user->can('activite.voir_toutes_directions'));
        Gate::define('indicateur.voir', fn($user) => $user->can('indicateur.voir'));
        Gate::define('indicateur.creer', fn($user) => $user->can('indicateur.creer'));
        Gate::define('document.voir', fn($user) => $user->can('document.voir'));
        Gate::define('document.deposer', fn($user) => $user->can('document.deposer'));
        Gate::define('alerte.voir', fn($user) => $user->can('alerte.voir'));
        Gate::define('alerte.traiter', fn($user) => $user->can('alerte.traiter'));
        Gate::define('alerte.escalader', fn($user) => $user->can('alerte.escalader'));
        Gate::define('alerte.configurer', fn($user) => $user->can('alerte.configurer'));
        Gate::define('admin.utilisateurs', fn($user) => $user->can('admin.utilisateurs'));
        Gate::define('admin.audit_log', fn($user) => $user->can('admin.audit_log'));
    }
}
