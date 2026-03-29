<?php

namespace App\Providers;

use App\Models\Activite;
use App\Models\Decision;
use App\Models\Document;
use App\Models\Indicateur;
use App\Models\Papa;
use App\Models\Rapport;
use App\Models\WorkflowInstance;
use App\Policies\ActivitePolicy;
use App\Policies\DecisionPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\IndicateurPolicy;
use App\Policies\PapaPolicy;
use App\Policies\RapportPolicy;
use App\Policies\WorkflowPolicy;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bloquer migrate:fresh et db:wipe sur la base de données de production
        Event::listen(CommandStarting::class, function (CommandStarting $event): void {
            $dangerousCommands = ['migrate:fresh', 'migrate:reset', 'db:wipe'];
            $db = config('database.connections.' . config('database.default') . '.database');

            if (in_array($event->command, $dangerousCommands, true)
                && app()->environment('local', 'staging')
                && $db === 'tb_papa_ceeac'
            ) {
                $event->output->writeln(
                    "<error>⛔  DANGER : la commande [{$event->command}] va effacer TOUTES les données de la base '{$db}'.</error>"
                );
                $event->output->writeln(
                    "<comment>Pour continuer, renommez temporairement DB_DATABASE dans .env ou utilisez une base de test.</comment>"
                );
                exit(1);
            }
        });
    }

    public function boot(): void
    {
        // ── Policies ────────────────────────────────────────────────────────
        Gate::policy(Papa::class, PapaPolicy::class);
        Gate::policy(Activite::class, ActivitePolicy::class);
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(Indicateur::class, IndicateurPolicy::class);
        Gate::policy(Decision::class, DecisionPolicy::class);
        Gate::policy(Rapport::class, RapportPolicy::class);
        Gate::policy(WorkflowInstance::class, WorkflowPolicy::class);

        // ── Super Admin bypasse toutes les policies ──────────────────────────
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });

        // ── Gates pour les permissions Spatie (authorize()) ──────────────────
        // Permet d'utiliser $this->authorize('papa.voir') dans les contrôleurs
        // sans passer par une policy explicite pour chaque permission
        $hasPermission = static fn ($user, string $permission) => $user->checkPermissionTo($permission, 'web');

        Gate::define('papa.voir', fn($user) => $hasPermission($user, 'papa.voir'));
        Gate::define('papa.creer', fn($user) => $hasPermission($user, 'papa.creer'));
        Gate::define('papa.modifier', fn($user) => $hasPermission($user, 'papa.modifier'));
        Gate::define('papa.supprimer', fn($user) => $hasPermission($user, 'papa.supprimer'));
        Gate::define('papa.voir_archive', fn($user) => $hasPermission($user, 'papa.voir_archive'));
        Gate::define('activite.voir', fn($user) => $hasPermission($user, 'activite.voir'));
        Gate::define('activite.creer', fn($user) => $hasPermission($user, 'activite.creer'));
        Gate::define('activite.voir_toutes_directions', fn($user) => $hasPermission($user, 'activite.voir_toutes_directions'));
        Gate::define('indicateur.voir', fn($user) => $hasPermission($user, 'indicateur.voir'));
        Gate::define('indicateur.creer', fn($user) => $hasPermission($user, 'indicateur.creer'));
        Gate::define('document.voir', fn($user) => $hasPermission($user, 'document.voir'));
        Gate::define('document.deposer', fn($user) => $hasPermission($user, 'document.deposer'));
        Gate::define('alerte.voir', fn($user) => $hasPermission($user, 'alerte.voir'));
        Gate::define('alerte.traiter', fn($user) => $hasPermission($user, 'alerte.traiter'));
        Gate::define('alerte.escalader', fn($user) => $hasPermission($user, 'alerte.escalader'));
        Gate::define('alerte.configurer', fn($user) => $hasPermission($user, 'alerte.configurer'));
        Gate::define('admin.utilisateurs', fn($user) => $hasPermission($user, 'admin.utilisateurs'));
        Gate::define('admin.audit_log', fn($user) => $hasPermission($user, 'admin.audit_log'));
        Gate::define('rapport.voir', fn($user) => $hasPermission($user, 'rapport.voir'));
        Gate::define('rapport.valider', fn($user) => $hasPermission($user, 'rapport.valider'));
        Gate::define('rapport.creer', fn($user) => $hasPermission($user, 'rapport.creer'));
        Gate::define('rapport.publier', fn($user) => $hasPermission($user, 'rapport.publier'));
        Gate::define('rapport.exporter', fn($user) => $hasPermission($user, 'rapport.exporter'));
        Gate::define('rapport.dashboard.voir', fn($user) => $hasPermission($user, 'rapport.dashboard.voir'));
        Gate::define('rapport.bibliotheque.voir', fn($user) => $hasPermission($user, 'rapport.bibliotheque.voir'));
        Gate::define('rapport.bibliotheque.telecharger', fn($user) => $hasPermission($user, 'rapport.bibliotheque.telecharger'));
        Gate::define('risque.voir', fn($user) => $hasPermission($user, 'risque.voir'));
        Gate::define('risque.creer', fn($user) => $hasPermission($user, 'risque.creer'));
        Gate::define('risque.modifier', fn($user) => $hasPermission($user, 'risque.modifier'));
        Gate::define('risque.supprimer', fn($user) => $hasPermission($user, 'risque.supprimer'));
        Gate::define('budget.voir', fn($user) => $hasPermission($user, 'budget.voir'));
        Gate::define('budget.creer', fn($user) => $hasPermission($user, 'budget.creer'));
        Gate::define('budget.modifier', fn($user) => $hasPermission($user, 'budget.modifier'));
        Gate::define('budget.supprimer', fn($user) => $hasPermission($user, 'budget.supprimer'));
        Gate::define('workflow.voir', fn($user) => $hasPermission($user, 'workflow.voir'));
        Gate::define('workflow.demarrer', fn($user) => $hasPermission($user, 'workflow.demarrer'));
        Gate::define('workflow.approuver', fn($user) => $hasPermission($user, 'workflow.approuver'));
        Gate::define('workflow.rejeter', fn($user) => $hasPermission($user, 'workflow.rejeter'));
        Gate::define('workflow.commenter', fn($user) => $hasPermission($user, 'workflow.commenter'));
        Gate::define('decision.voir', fn($user) => $hasPermission($user, 'decision.voir'));
        Gate::define('decision.creer', fn($user) => $hasPermission($user, 'decision.creer'));
        Gate::define('decision.valider', fn($user) => $hasPermission($user, 'decision.valider'));
        Gate::define('decision.executer', fn($user) => $hasPermission($user, 'decision.executer'));
        Gate::define('audit_event.voir', fn($user) => $hasPermission($user, 'audit_event.voir'));
        Gate::define('notification_rule.gerer', fn($user) => $hasPermission($user, 'notification_rule.gerer'));

        // ── Paramètres ───────────────────────────────────────────────────
        Gate::define('parametres.generaux.voir',        fn($u) => $hasPermission($u, 'parametres.generaux.voir'));
        Gate::define('parametres.generaux.modifier',    fn($u) => $hasPermission($u, 'parametres.generaux.modifier'));
        Gate::define('parametres.papa.voir',            fn($u) => $hasPermission($u, 'parametres.papa.voir'));
        Gate::define('parametres.papa.modifier',        fn($u) => $hasPermission($u, 'parametres.papa.modifier'));
        Gate::define('parametres.papa.archiver',        fn($u) => $hasPermission($u, 'parametres.papa.archiver'));
        Gate::define('parametres.referentiels.voir',    fn($u) => $hasPermission($u, 'parametres.referentiels.voir'));
        Gate::define('parametres.referentiels.gerer',   fn($u) => $hasPermission($u, 'parametres.referentiels.gerer'));
        Gate::define('parametres.libelles.voir',        fn($u) => $hasPermission($u, 'parametres.libelles.voir'));
        Gate::define('parametres.libelles.modifier',    fn($u) => $hasPermission($u, 'parametres.libelles.modifier'));
        Gate::define('parametres.rbm.voir',             fn($u) => $hasPermission($u, 'parametres.rbm.voir'));
        Gate::define('parametres.rbm.modifier',         fn($u) => $hasPermission($u, 'parametres.rbm.modifier'));
        Gate::define('parametres.budget.voir',          fn($u) => $hasPermission($u, 'parametres.budget.voir'));
        Gate::define('parametres.budget.modifier',      fn($u) => $hasPermission($u, 'parametres.budget.modifier'));
        Gate::define('parametres.alertes.voir',         fn($u) => $hasPermission($u, 'parametres.alertes.voir'));
        Gate::define('parametres.alertes.modifier',     fn($u) => $hasPermission($u, 'parametres.alertes.modifier'));
        Gate::define('parametres.ged.voir',             fn($u) => $hasPermission($u, 'parametres.ged.voir'));
        Gate::define('parametres.ged.modifier',         fn($u) => $hasPermission($u, 'parametres.ged.modifier'));
        Gate::define('parametres.workflows.voir',       fn($u) => $hasPermission($u, 'parametres.workflows.voir'));
        Gate::define('parametres.workflows.modifier',   fn($u) => $hasPermission($u, 'parametres.workflows.modifier'));
        Gate::define('parametres.droits.voir',          fn($u) => $hasPermission($u, 'parametres.droits.voir'));
        Gate::define('parametres.droits.modifier',      fn($u) => $hasPermission($u, 'parametres.droits.modifier'));
        Gate::define('parametres.affichage.voir',       fn($u) => $hasPermission($u, 'parametres.affichage.voir'));
        Gate::define('parametres.affichage.modifier',   fn($u) => $hasPermission($u, 'parametres.affichage.modifier'));
        Gate::define('parametres.technique.voir',       fn($u) => $hasPermission($u, 'parametres.technique.voir'));
        Gate::define('parametres.technique.modifier',   fn($u) => $hasPermission($u, 'parametres.technique.modifier'));
        Gate::define('parametres.journal.voir',         fn($u) => $hasPermission($u, 'parametres.journal.voir'));
        Gate::define('parametres.sauvegardes.voir',     fn($u) => $hasPermission($u, 'parametres.sauvegardes.voir'));
        Gate::define('parametres.sauvegardes.exporter', fn($u) => $hasPermission($u, 'parametres.sauvegardes.exporter'));
        Gate::define('parametres.sauvegardes.importer', fn($u) => $hasPermission($u, 'parametres.sauvegardes.importer'));
    }
}
