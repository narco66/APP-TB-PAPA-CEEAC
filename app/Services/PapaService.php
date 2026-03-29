<?php

namespace App\Services;

use App\Models\Papa;
use App\Models\User;
use App\Models\ValidationWorkflow;
use Illuminate\Support\Facades\DB;

class PapaService
{
    public function __construct(
        private ?AuditService $auditService = null,
        private ?NotificationDispatchService $notificationDispatchService = null,
    ) {}

    /**
     * Calculer et mettre a jour les taux de realisation d'un PAPA
     * (denormalisation ascendante : Activites -> Resultats -> OI -> AP -> PAPA)
     */
    public function recalculerTaux(Papa $papa): void
    {
        DB::transaction(function () use ($papa) {
            foreach ($papa->actionsPrioritaires()->with([
                'objectifsImmediat.resultatsAttendus.activites',
                'objectifsImmediat.resultatsAttendus.indicateurs',
            ])->get() as $ap) {
                foreach ($ap->objectifsImmediat as $oi) {
                    foreach ($oi->resultatsAttendus as $ra) {
                        $activites = $ra->activites;

                        if ($activites->isNotEmpty()) {
                            $taux = $activites->avg('taux_realisation');
                            $ra->update(['taux_atteinte' => round($taux, 2)]);
                        }
                    }

                    $tauxRa = $oi->resultatsAttendus->avg('taux_atteinte') ?? 0;
                    $oi->update(['taux_atteinte' => round($tauxRa, 2)]);
                }

                $tauxOi = $ap->objectifsImmediat->avg('taux_atteinte') ?? 0;
                $ap->update(['taux_realisation' => round($tauxOi, 2)]);
            }

            $tauxPhysique = $papa->actionsPrioritaires()->avg('taux_realisation') ?? 0;

            $budget = $papa->budgets()->first();
            $tauxFinancier = 0;

            if ($budget && $budget->montant_prevu > 0) {
                $totalDecaisse = $papa->budgets()->sum('montant_decaisse');
                $totalPrevu = $papa->budgets()->sum('montant_prevu');
                $tauxFinancier = $totalPrevu > 0 ? ($totalDecaisse / $totalPrevu) * 100 : 0;
            }

            $papa->update([
                'taux_execution_physique' => round($tauxPhysique, 2),
                'taux_execution_financiere' => round($tauxFinancier, 2),
            ]);
        });
    }

    /**
     * Soumettre un PAPA pour validation hierarchique
     */
    public function soumettre(Papa $papa, User $user, string $commentaire = ''): void
    {
        DB::transaction(function () use ($papa, $user, $commentaire) {
            $statutAvant = $papa->statut;

            $papa->update(['statut' => 'soumis']);

            ValidationWorkflow::create([
                'validable_type' => Papa::class,
                'validable_id' => $papa->id,
                'papa_id' => $papa->id,
                'etape' => 'soumission',
                'action' => 'soumis',
                'acteur_id' => $user->id,
                'commentaire' => $commentaire,
                'statut_avant' => $statutAvant,
                'statut_apres' => 'soumis',
            ]);

            $this->auditService?->enregistrer(
                module: 'papa',
                eventType: 'papa_soumis',
                auditable: $papa,
                acteur: $user,
                action: 'soumission',
                description: "PAPA {$papa->code} soumis pour validation",
                donneesAvant: ['statut' => $statutAvant],
                donneesApres: ['statut' => 'soumis'],
                papa: $papa
            );

            $this->notificationDispatchService?->dispatch(
                eventType: 'workflow_demarre',
                titre: "PAPA {$papa->code} soumis",
                message: $commentaire ?: 'Un PAPA a ete soumis pour validation.',
                lien: route('papas.show', $papa),
                notifiable: $papa
            );
        });
    }

    /**
     * Valider un PAPA
     */
    public function valider(Papa $papa, User $user, string $commentaire = ''): void
    {
        DB::transaction(function () use ($papa, $user, $commentaire) {
            $statutAvant = $papa->statut;

            $papa->update([
                'statut' => 'valide',
                'validated_by' => $user->id,
                'validated_at' => now(),
            ]);

            ValidationWorkflow::create([
                'validable_type' => Papa::class,
                'validable_id' => $papa->id,
                'papa_id' => $papa->id,
                'etape' => 'validation_president',
                'action' => 'approuve',
                'acteur_id' => $user->id,
                'commentaire' => $commentaire,
                'statut_avant' => $statutAvant,
                'statut_apres' => 'valide',
            ]);

            $this->auditService?->enregistrer(
                module: 'papa',
                eventType: 'papa_valide',
                auditable: $papa,
                acteur: $user,
                action: 'validation',
                description: "PAPA {$papa->code} valide",
                donneesAvant: ['statut' => $statutAvant],
                donneesApres: ['statut' => 'valide'],
                papa: $papa
            );

            $this->notificationDispatchService?->dispatch(
                eventType: 'decision_validee',
                titre: "PAPA {$papa->code} valide",
                message: $commentaire ?: 'Le PAPA a ete valide et peut etre execute.',
                lien: route('papas.show', $papa),
                notifiable: $papa
            );
        });
    }

    /**
     * Rejeter un PAPA (retour au brouillon)
     */
    public function rejeter(Papa $papa, User $user, string $motif): void
    {
        DB::transaction(function () use ($papa, $user, $motif) {
            $statutAvant = $papa->statut;

            $papa->update(['statut' => 'brouillon']);

            ValidationWorkflow::create([
                'validable_type' => Papa::class,
                'validable_id' => $papa->id,
                'papa_id' => $papa->id,
                'etape' => 'rejet',
                'action' => 'rejete',
                'acteur_id' => $user->id,
                'motif_rejet' => $motif,
                'statut_avant' => $statutAvant,
                'statut_apres' => 'brouillon',
            ]);

            $this->auditService?->enregistrer(
                module: 'papa',
                eventType: 'papa_rejete',
                auditable: $papa,
                acteur: $user,
                action: 'rejet',
                description: "PAPA {$papa->code} rejete",
                niveau: 'warning',
                donneesAvant: ['statut' => $statutAvant],
                donneesApres: ['statut' => 'brouillon', 'motif' => $motif],
                papa: $papa
            );

            $this->notificationDispatchService?->dispatch(
                eventType: 'workflow_rejete',
                titre: "PAPA {$papa->code} rejete",
                message: $motif,
                lien: route('papas.show', $papa),
                notifiable: $papa,
                context: ['niveau' => 'warning', 'users' => collect([$user])]
            );
        });
    }

    /**
     * Archiver un PAPA (lecture seule definitive)
     */
    public function archiver(Papa $papa, User $user, string $motif = ''): void
    {
        DB::transaction(function () use ($papa, $user, $motif) {
            $statutAvant = $papa->statut;

            $papa->update([
                'statut' => 'archive',
                'est_verrouille' => true,
                'archived_by' => $user->id,
                'archived_at' => now(),
                'motif_archivage' => $motif,
            ]);

            ValidationWorkflow::create([
                'validable_type' => Papa::class,
                'validable_id' => $papa->id,
                'papa_id' => $papa->id,
                'etape' => 'cloture',
                'action' => 'information',
                'acteur_id' => $user->id,
                'commentaire' => trim('PAPA archive. ' . $motif),
                'statut_avant' => $statutAvant,
                'statut_apres' => 'archive',
            ]);

            $this->auditService?->enregistrer(
                module: 'papa',
                eventType: 'papa_archive',
                auditable: $papa,
                acteur: $user,
                action: 'archivage',
                description: "PAPA {$papa->code} archive",
                donneesAvant: ['statut' => $statutAvant],
                donneesApres: ['statut' => 'archive', 'motif_archivage' => $motif],
                papa: $papa
            );
        });
    }

    /**
     * Cloner un PAPA vers l'exercice suivant
     */
    public function cloner(Papa $papa, int $anneeNouvelle, User $user): Papa
    {
        return DB::transaction(function () use ($papa, $anneeNouvelle, $user) {
            $nouveau = $papa->replicate([
                'statut',
                'validated_by',
                'validated_at',
                'archived_by',
                'archived_at',
                'archived_at',
                'taux_execution_physique',
                'taux_execution_financiere',
            ]);

            $nouveau->code = "PAPA-{$anneeNouvelle}";
            $nouveau->libelle = str_replace((string) $papa->annee, (string) $anneeNouvelle, $papa->libelle);
            $nouveau->annee = $anneeNouvelle;
            $nouveau->date_debut = "{$anneeNouvelle}-01-01";
            $nouveau->date_fin = "{$anneeNouvelle}-12-31";
            $nouveau->statut = 'brouillon';
            $nouveau->est_verrouille = false;
            $nouveau->clone_de_papa_id = $papa->id;
            $nouveau->created_by = $user->id;
            $nouveau->taux_execution_physique = 0;
            $nouveau->taux_execution_financiere = 0;
            $nouveau->save();

            return $nouveau;
        });
    }
}
