<?php

namespace App\Services;

use App\Models\Activite;
use App\Models\Alerte;
use App\Models\Papa;
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

        return $alertes;
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
        ]);
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
