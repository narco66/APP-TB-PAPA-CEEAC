<?php

namespace App\Services;

use App\Models\Activite;
use App\Models\Alerte;
use App\Models\BudgetPapa;
use App\Models\Indicateur;
use App\Models\Papa;
use App\Models\User;
use App\Notifications\AlerteNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AlerteService
{
    /**
     * Analyser un PAPA et générer automatiquement des alertes
     */
    public function genererAlertesPapa(Papa $papa): Collection
    {
        $alertes = collect();

        // ── 1. Activités en retard ─────────────────────────────────────────
        $activitesEnRetard = Activite::whereHas('resultatAttendu.objectifImmediats.actionPrioritaire', function ($q) use ($papa) {
            $q->where('papa_id', $papa->id);
        })
        ->where('statut', '!=', 'terminee')
        ->where('statut', '!=', 'abandonnee')
        ->where('date_fin_prevue', '<', now()->toDateString())
        ->with('direction', 'responsable')
        ->get();

        foreach ($activitesEnRetard as $activite) {
            $existante = Alerte::where('alertable_type', Activite::class)
                ->where('alertable_id', $activite->id)
                ->where('type_alerte', 'retard_activite')
                ->where('statut', 'nouvelle')
                ->first();

            if (!$existante) {
                $alerte = Alerte::create([
                    'papa_id'       => $papa->id,
                    'alertable_type' => Activite::class,
                    'alertable_id'  => $activite->id,
                    'type_alerte'   => 'retard_activite',
                    'niveau'        => 'attention',
                    'titre'         => "Retard : {$activite->code}",
                    'message'       => "L'activité \"{$activite->libelle}\" aurait dû se terminer le " .
                                       $activite->date_fin_prevue->format('d/m/Y') . ". Taux actuel : {$activite->taux_realisation}%.",
                    'statut'        => 'nouvelle',
                    'destinataire_id' => $activite->responsable_id,
                    'direction_id'  => $activite->direction_id,
                    'auto_generee'  => true,
                ]);
                $alertes->push($alerte);
            }
        }

        // ── 2. Taux de réalisation PAPA faible ─────────────────────────────
        if ($papa->taux_execution_physique < 30 && Carbon::now()->month >= 6) {
            $existante = Alerte::where('papa_id', $papa->id)
                ->where('type_alerte', 'taux_realisation_faible')
                ->where('statut', 'nouvelle')
                ->first();

            if (!$existante) {
                $alerte = Alerte::create([
                    'papa_id'     => $papa->id,
                    'alertable_type' => Papa::class,
                    'alertable_id' => $papa->id,
                    'type_alerte' => 'taux_realisation_faible',
                    'niveau'      => 'critique',
                    'titre'       => "PAPA {$papa->code} : taux physique insuffisant",
                    'message'     => "Le taux d'exécution physique du PAPA {$papa->code} est de {$papa->taux_execution_physique}% à mi-parcours. Un risque de non-atteinte des objectifs est identifié.",
                    'statut'      => 'nouvelle',
                    'auto_generee' => true,
                ]);
                $alertes->push($alerte);
            }
        }

        // ── 3. Indicateurs en sous-performance (seuils dépassés) ──────────────
        $indicateurs = Indicateur::whereHas('resultatAttendu.objectifImmediats.actionPrioritaire', function ($q) use ($papa) {
            $q->where('papa_id', $papa->id);
        })
        ->whereNotNull('seuil_alerte_rouge')
        ->with('dernierValeur')
        ->get();

        foreach ($indicateurs as $indicateur) {
            $niveauAlerte = $indicateur->niveauAlerte();
            if (!in_array($niveauAlerte, ['rouge', 'orange'])) {
                continue;
            }

            $existante = Alerte::where('alertable_type', Indicateur::class)
                ->where('alertable_id', $indicateur->id)
                ->where('type_alerte', 'indicateur_sous_performance')
                ->where('statut', 'nouvelle')
                ->first();

            if (!$existante) {
                $niveau = $niveauAlerte === 'rouge' ? 'critique' : 'attention';
                $alerte = Alerte::create([
                    'papa_id'        => $papa->id,
                    'alertable_type' => Indicateur::class,
                    'alertable_id'   => $indicateur->id,
                    'type_alerte'    => 'indicateur_sous_performance',
                    'niveau'         => $niveau,
                    'titre'          => "KPI {$indicateur->code} : sous-performance ({$niveauAlerte})",
                    'message'        => "L'indicateur \"{$indicateur->libelle}\" est en niveau d'alerte {$niveauAlerte}. Taux de réalisation : {$indicateur->taux_realisation_courant}%.",
                    'statut'         => 'nouvelle',
                    'destinataire_id' => $indicateur->responsable_id,
                    'direction_id'   => $indicateur->direction_id,
                    'auto_generee'   => true,
                ]);
                $alertes->push($alerte);
            }
        }

        // ── 4. Dépassements budgétaires ────────────────────────────────────
        $budgets = BudgetPapa::where('papa_id', $papa->id)
            ->where('montant_prevu', '>', 0)
            ->whereRaw('montant_engage > montant_prevu')
            ->get();

        foreach ($budgets as $budget) {
            $existante = Alerte::where('alertable_type', BudgetPapa::class)
                ->where('alertable_id', $budget->id)
                ->where('type_alerte', 'budget_depasse')
                ->where('statut', 'nouvelle')
                ->first();

            if (!$existante) {
                $ecart = $budget->montant_engage - $budget->montant_prevu;
                $alerte = Alerte::create([
                    'papa_id'        => $papa->id,
                    'alertable_type' => BudgetPapa::class,
                    'alertable_id'   => $budget->id,
                    'type_alerte'    => 'budget_depasse',
                    'niveau'         => 'critique',
                    'titre'          => "Dépassement budgétaire : {$budget->libelle_ligne}",
                    'message'        => "La ligne budgétaire \"{$budget->libelle_ligne}\" dépasse son enveloppe prévue de " .
                                        number_format($ecart, 0, ',', ' ') . " {$budget->devise}. " .
                                        "Prévu : " . number_format($budget->montant_prevu, 0, ',', ' ') . " / Engagé : " . number_format($budget->montant_engage, 0, ',', ' ') . ".",
                    'statut'         => 'nouvelle',
                    'auto_generee'   => true,
                ]);
                $alertes->push($alerte);
            }
        }

        // Notifier les destinataires de toutes les nouvelles alertes créées
        foreach ($alertes as $alerte) {
            $this->notifier($alerte);
        }

        return $alertes;
    }

    /**
     * Envoyer la notification email+in-app au destinataire d'une alerte
     */
    public function notifier(Alerte $alerte): void
    {
        if (!$alerte->destinataire_id) {
            return;
        }
        $destinataire = User::find($alerte->destinataire_id);
        if ($destinataire?->actif) {
            $destinataire->notify(new AlerteNotification($alerte));
        }
    }

    /**
     * Marquer une alerte comme vue
     */
    public function marquerVue(Alerte $alerte): void
    {
        $alerte->update([
            'statut' => 'vue',
            'lue_le' => now(),
        ]);
    }

    /**
     * Escalader une alerte
     */
    public function escalader(Alerte $alerte, int $destinataireId): void
    {
        $alerte->update([
            'escaladee'          => true,
            'escaladee_vers_id'  => $destinataireId,
            'escaladee_le'       => now(),
            'destinataire_id'    => $destinataireId,
        ]);

        $this->notifier($alerte->fresh());
    }

    /**
     * Résoudre une alerte
     */
    public function resoudre(Alerte $alerte, int $userId, string $resolution): void
    {
        $alerte->update([
            'statut'      => 'resolue',
            'traitee_par' => $userId,
            'traitee_le'  => now(),
            'resolution'  => $resolution,
        ]);
    }

    /**
     * Compter les alertes non traitées par niveau
     */
    public function compterParNiveau(Papa $papa): array
    {
        return Alerte::where('papa_id', $papa->id)
            ->whereIn('statut', ['nouvelle', 'vue'])
            ->selectRaw('niveau, count(*) as total')
            ->groupBy('niveau')
            ->pluck('total', 'niveau')
            ->toArray();
    }
}
