<?php

namespace App\Services;

use App\Models\Papa;
use App\Models\User;
use App\Models\ValidationWorkflow;
use App\Models\WorkflowAction;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WorkflowEngine
{
    public function __construct(
        private AuditService $auditService,
        private AuthorizationMatrixService $authorizationMatrixService,
        private NotificationDispatchService $notificationDispatchService,
    ) {}

    public function demarrer(
        WorkflowDefinition $definition,
        Model $objet,
        User $acteur,
        ?Papa $papa = null,
        array $metadata = []
    ): WorkflowInstance {
        return DB::transaction(function () use ($definition, $objet, $acteur, $papa, $metadata) {
            $initialStep = $definition->steps()->where('est_etape_initiale', true)->first()
                ?? $definition->steps()->orderBy('ordre')->firstOrFail();

            $instance = WorkflowInstance::create([
                'workflow_definition_id' => $definition->id,
                'objet_type' => $objet::class,
                'objet_id' => $objet->getKey(),
                'papa_id' => $papa?->id ?? $objet->papa_id ?? null,
                'statut' => 'en_cours',
                'etape_courante_id' => $initialStep->id,
                'demarre_par' => $acteur->id,
                'date_demarrage' => now(),
                'metadata' => $metadata,
            ]);

            $this->journaliserAction($instance, $initialStep, $acteur, 'soumis', 'soumis', null, null);

            $this->auditService->enregistrer(
                module: 'workflow',
                eventType: 'workflow_demarre',
                auditable: $objet,
                acteur: $acteur,
                action: 'soumis',
                description: "Démarrage du workflow {$definition->code}",
                donneesApres: ['workflow_instance_id' => $instance->id, 'etape' => $initialStep->code],
                papa: $papa
            );

            $this->notificationDispatchService->dispatch(
                eventType: 'workflow_demarre',
                titre: "Workflow {$definition->libelle} démarré",
                message: 'Un nouveau dossier a été soumis dans le circuit institutionnel.',
                lien: route('workflows.show', $instance),
                notifiable: $instance,
                context: ['step' => $initialStep]
            );

            return $instance->fresh(['definition', 'etapeCourante', 'actions']);
        });
    }

    public function approuver(WorkflowInstance $instance, User $acteur, ?string $commentaire = null): WorkflowInstance
    {
        return DB::transaction(function () use ($instance, $acteur, $commentaire) {
            $instance->loadMissing('objet', 'etapeCourante', 'definition.steps');
            $step = $instance->etapeCourante;

            abort_unless($step && $this->authorizationMatrixService->userCanHandleStep($acteur, $step), 403);

            $nextStep = $this->nextStep($instance);

            $this->journaliserAction($instance, $step, $acteur, 'approuve', 'approuve', $commentaire, null);

            $before = [
                'statut' => $instance->statut,
                'etape_courante_id' => $instance->etape_courante_id,
            ];

            $instance->update([
                'etape_courante_id' => $nextStep?->id,
                'statut' => $nextStep ? 'en_cours' : 'approuve',
                'date_cloture' => $nextStep ? null : now(),
            ]);

            $this->auditService->enregistrer(
                module: 'workflow',
                eventType: 'workflow_approuve',
                auditable: $instance->objet,
                acteur: $acteur,
                action: 'approuve',
                description: "Étape {$step->code} approuvée",
                donneesAvant: $before,
                donneesApres: [
                    'statut' => $instance->statut,
                    'etape_courante_id' => $instance->etape_courante_id,
                ],
                papa: $instance->papa
            );

            $this->syncLegacyValidation(
                $instance,
                $acteur,
                $nextStep ? 'information' : 'approuve',
                $commentaire,
                null
            );

            return $instance->fresh(['etapeCourante', 'actions']);
        });
    }

    public function rejeter(WorkflowInstance $instance, User $acteur, string $motif, ?string $commentaire = null): WorkflowInstance
    {
        return DB::transaction(function () use ($instance, $acteur, $motif, $commentaire) {
            $instance->loadMissing('objet', 'etapeCourante');
            $step = $instance->etapeCourante;

            abort_unless($step && $this->authorizationMatrixService->userCanHandleStep($acteur, $step), 403);

            $before = [
                'statut' => $instance->statut,
                'etape_courante_id' => $instance->etape_courante_id,
            ];

            $this->journaliserAction($instance, $step, $acteur, 'rejete', 'rejete', $commentaire, $motif);

            $instance->update([
                'statut' => 'rejete',
                'date_cloture' => now(),
                'motif_cloture' => $motif,
            ]);

            $this->auditService->enregistrer(
                module: 'workflow',
                eventType: 'workflow_rejete',
                auditable: $instance->objet,
                acteur: $acteur,
                action: 'rejete',
                description: "Workflow rejeté à l'étape {$step->code}",
                niveau: 'warning',
                donneesAvant: $before,
                donneesApres: [
                    'statut' => $instance->statut,
                    'motif_cloture' => $motif,
                ],
                papa: $instance->papa
            );

            $this->notificationDispatchService->dispatch(
                eventType: 'workflow_rejete',
                titre: "Workflow rejeté pour {$instance->objet_type}",
                message: $motif,
                lien: route('workflows.show', $instance),
                notifiable: $instance,
                context: ['users' => collect([$instance->demarrePar])->filter()]
            );

            $this->syncLegacyValidation($instance, $acteur, 'rejete', $commentaire, $motif);

            return $instance->fresh(['etapeCourante', 'actions']);
        });
    }

    public function commenter(WorkflowInstance $instance, User $acteur, string $commentaire): WorkflowAction
    {
        return DB::transaction(function () use ($instance, $acteur, $commentaire) {
            $instance->loadMissing('objet', 'etapeCourante');
            $step = $instance->etapeCourante;

            abort_unless($step && $this->authorizationMatrixService->userCanHandleStep($acteur, $step), 403);

            $action = $this->journaliserAction($instance, $step, $acteur, 'commente', null, $commentaire, null);

            $this->auditService->enregistrer(
                module: 'workflow',
                eventType: 'workflow_commente',
                auditable: $instance->objet,
                acteur: $acteur,
                action: 'commente',
                description: "Commentaire ajouté sur l'étape {$step->code}",
                donneesApres: ['commentaire' => $commentaire],
                papa: $instance->papa
            );

            return $action;
        });
    }

    private function nextStep(WorkflowInstance $instance): ?WorkflowStep
    {
        return $instance->definition
            ->steps
            ->where('ordre', '>', $instance->etapeCourante?->ordre ?? 0)
            ->sortBy('ordre')
            ->first();
    }

    private function journaliserAction(
        WorkflowInstance $instance,
        WorkflowStep $step,
        User $acteur,
        string $action,
        ?string $decision,
        ?string $commentaire,
        ?string $motifRejet
    ): WorkflowAction {
        return WorkflowAction::create([
            'workflow_instance_id' => $instance->id,
            'workflow_step_id' => $step->id,
            'acteur_id' => $acteur->id,
            'action' => $action,
            'decision' => $decision,
            'commentaire' => $commentaire,
            'motif_rejet' => $motifRejet,
            'effectue_le' => now(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    private function syncLegacyValidation(
        WorkflowInstance $instance,
        User $acteur,
        string $action,
        ?string $commentaire,
        ?string $motifRejet
    ): void {
        if (! $instance->papa_id) {
            return;
        }

        ValidationWorkflow::create([
            'validable_type' => $instance->objet_type,
            'validable_id' => $instance->objet_id,
            'papa_id' => $instance->papa_id,
            'etape' => $this->mapLegacyEtape($instance->etapeCourante?->code),
            'action' => $action,
            'acteur_id' => $acteur->id,
            'commentaire' => $commentaire,
            'motif_rejet' => $motifRejet,
            'statut_avant' => 'en_cours',
            'statut_apres' => $instance->statut,
        ]);
    }

    private function mapLegacyEtape(?string $stepCode): string
    {
        if ($stepCode === null || $stepCode === 'soumission') {
            return 'soumission';
        }

        if (in_array($stepCode, [
            'validation_direction',
            'validation_commissaire',
            'validation_sg',
            'validation_vp',
            'validation_president',
            'rejet',
            'cloture',
        ], true)) {
            return $stepCode;
        }

        if (str_contains($stepCode, 'president')) {
            return 'validation_president';
        }

        if (str_contains($stepCode, 'sg')) {
            return 'validation_sg';
        }

        if (str_contains($stepCode, 'commissaire')) {
            return 'validation_commissaire';
        }

        if (str_contains($stepCode, 'direction')) {
            return 'validation_direction';
        }

        return 'soumission';
    }
}
