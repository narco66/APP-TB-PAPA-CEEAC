<?php

namespace App\Services;

use App\Models\ActionPrioritaire;
use App\Models\Activite;
use App\Models\Indicateur;
use App\Models\ObjectifImmediats;
use App\Models\Papa;
use App\Models\ResultatAttendu;
use App\Models\User;
use App\Models\ValidationWorkflow;
use Illuminate\Support\Facades\DB;

class PapaService
{
    /**
     * Calculer et mettre à jour les taux de réalisation d'un PAPA
     * (dénormalisation ascendante : Activités → Résultats → OI → AP → PAPA)
     */
    public function recalculerTaux(Papa $papa): void
    {
        DB::transaction(function () use ($papa) {

            // 1. Recalculer taux des activités (fait par l'utilisateur ou les tâches)
            // 2. Consolider les résultats attendus
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

                    // Consolidation OI
                    $tauxRa = $oi->resultatsAttendus->avg('taux_atteinte') ?? 0;
                    $oi->update(['taux_atteinte' => round($tauxRa, 2)]);
                }

                // Consolidation AP
                $tauxOi = $ap->objectifsImmediat->avg('taux_atteinte') ?? 0;
                $ap->update(['taux_realisation' => round($tauxOi, 2)]);
            }

            // 3. Taux global PAPA
            $tauxPhysique = $papa->actionsPrioritaires()->avg('taux_realisation') ?? 0;

            // Taux financier : (montant_décaissé / montant_prévu) × 100
            $budget = $papa->budgets()->first();
            $tauxFinancier = 0;
            if ($budget && $budget->montant_prevu > 0) {
                $totalDecaisse = $papa->budgets()->sum('montant_decaisse');
                $totalPrevu    = $papa->budgets()->sum('montant_prevu');
                $tauxFinancier = $totalPrevu > 0 ? ($totalDecaisse / $totalPrevu) * 100 : 0;
            }

            $papa->update([
                'taux_execution_physique'   => round($tauxPhysique, 2),
                'taux_execution_financiere' => round($tauxFinancier, 2),
            ]);
        });
    }

    /**
     * Soumettre un PAPA pour validation hiérarchique
     */
    public function soumettre(Papa $papa, User $user, string $commentaire = ''): void
    {
        DB::transaction(function () use ($papa, $user, $commentaire) {
            $statutAvant = $papa->statut;
            $papa->update(['statut' => 'soumis']);

            ValidationWorkflow::create([
                'validable_type' => Papa::class,
                'validable_id'   => $papa->id,
                'papa_id'        => $papa->id,
                'etape'          => 'soumission',
                'action'         => 'soumis',
                'acteur_id'      => $user->id,
                'commentaire'    => $commentaire,
                'statut_avant'   => $statutAvant,
                'statut_apres'   => 'soumis',
            ]);
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
                'statut'       => 'valide',
                'validated_by' => $user->id,
                'validated_at' => now(),
            ]);

            ValidationWorkflow::create([
                'validable_type' => Papa::class,
                'validable_id'   => $papa->id,
                'papa_id'        => $papa->id,
                'etape'          => 'validation_president',
                'action'         => 'approuve',
                'acteur_id'      => $user->id,
                'commentaire'    => $commentaire,
                'statut_avant'   => $statutAvant,
                'statut_apres'   => 'valide',
            ]);
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
                'validable_id'   => $papa->id,
                'papa_id'        => $papa->id,
                'etape'          => 'rejet',
                'action'         => 'rejete',
                'acteur_id'      => $user->id,
                'motif_rejet'    => $motif,
                'statut_avant'   => $statutAvant,
                'statut_apres'   => 'brouillon',
            ]);
        });
    }

    /**
     * Archiver un PAPA (lecture seule définitive)
     */
    public function archiver(Papa $papa, User $user, string $motif = ''): void
    {
        DB::transaction(function () use ($papa, $user, $motif) {
            $papa->update([
                'statut'          => 'archive',
                'est_verrouille'  => true,
                'archived_by'     => $user->id,
                'archived_at'     => now(),
                'motif_archivage' => $motif,
            ]);

            ValidationWorkflow::create([
                'validable_type' => Papa::class,
                'validable_id'   => $papa->id,
                'papa_id'        => $papa->id,
                'etape'          => 'cloture',
                'action'         => 'information',
                'acteur_id'      => $user->id,
                'commentaire'    => 'PAPA archivé. ' . $motif,
                'statut_avant'   => 'cloture',
                'statut_apres'   => 'archive',
            ]);
        });
    }

    /**
     * Cloner un PAPA vers l'exercice suivant
     */
    public function cloner(Papa $papa, int $anneeNouvelle, User $user): Papa
    {
        return DB::transaction(function () use ($papa, $anneeNouvelle, $user) {
            $nouveau = $papa->replicate([
                'statut', 'validated_by', 'validated_at',
                'archived_by', 'archived_at', 'archived_at',
                'taux_execution_physique', 'taux_execution_financiere',
            ]);
            $nouveau->code          = "PAPA-{$anneeNouvelle}";
            $nouveau->libelle       = str_replace((string)$papa->annee, (string)$anneeNouvelle, $papa->libelle);
            $nouveau->annee         = $anneeNouvelle;
            $nouveau->date_debut    = "{$anneeNouvelle}-01-01";
            $nouveau->date_fin      = "{$anneeNouvelle}-12-31";
            $nouveau->statut        = 'brouillon';
            $nouveau->est_verrouille = false;
            $nouveau->clone_de_papa_id = $papa->id;
            $nouveau->created_by    = $user->id;
            $nouveau->taux_execution_physique   = 0;
            $nouveau->taux_execution_financiere = 0;
            $nouveau->save();

            return $nouveau;
        });
    }
}
