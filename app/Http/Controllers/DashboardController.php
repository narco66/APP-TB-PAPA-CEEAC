<?php

namespace App\Http\Controllers;

use App\Models\Papa;
use App\Services\DashboardService;
use App\Services\AlerteService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private AlerteService $alerteService,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        // Trouver le PAPA actif courant
        $papa = Papa::enExecution()->orderByDesc('annee')->first()
            ?? Papa::orderByDesc('annee')->first();

        if (!$papa) {
            return view('dashboard.vide', compact('user'));
        }

        // Sélection de la vue selon le rôle principal
        $rolePrincipal = $user->getRoleNames()->first();

        return match($rolePrincipal) {
            'president', 'super_admin' => $this->dashboardPresident($papa, $user),
            'vice_president'           => $this->dashboardVicePresident($papa, $user),
            'commissaire'              => $this->dashboardCommissaire($papa, $user),
            'secretaire_general'       => $this->dashboardSG($papa, $user),
            'directeur_technique',
            'directeur_appui'          => $this->dashboardDirection($papa, $user),
            'chef_service',
            'point_focal'              => $this->dashboardService($papa, $user),
            'auditeur_interne',
            'controle_financier'       => $this->dashboardAudit($papa, $user),
            default                    => $this->dashboardGeneral($papa, $user),
        };
    }

    private function dashboardPresident(Papa $papa, $user)
    {
        $kpis    = $this->dashboardService->kpisExecutif($papa);
        $graphes = [
            'evolution'     => $this->dashboardService->evolutionTrimestrielle($papa),
            'departements'  => $this->dashboardService->comparatifDepartements($papa),
            'activites'     => $this->dashboardService->repartitionActivitesStatut($papa),
        ];
        $alertes = $this->alerteService->compterParNiveau($papa);

        return view('dashboard.president', compact('kpis', 'graphes', 'alertes', 'papa', 'user'));
    }

    private function dashboardVicePresident(Papa $papa, $user)
    {
        $kpis    = $this->dashboardService->kpisExecutif($papa);
        $graphes = [
            'evolution'    => $this->dashboardService->evolutionTrimestrielle($papa),
            'departements' => $this->dashboardService->comparatifDepartements($papa),
        ];

        return view('dashboard.vice_president', compact('kpis', 'graphes', 'papa', 'user'));
    }

    private function dashboardCommissaire(Papa $papa, $user)
    {
        // Filtrer par département du commissaire (via sa direction)
        $departement = $user->direction?->departement;
        $kpisGlobaux = $this->dashboardService->kpisExecutif($papa);

        return view('dashboard.commissaire', compact('kpisGlobaux', 'papa', 'user', 'departement'));
    }

    private function dashboardSG(Papa $papa, $user)
    {
        $kpis    = $this->dashboardService->kpisExecutif($papa);
        $graphes = [
            'evolution'    => $this->dashboardService->evolutionTrimestrielle($papa),
            'departements' => $this->dashboardService->comparatifDepartements($papa),
            'activites'    => $this->dashboardService->repartitionActivitesStatut($papa),
        ];

        return view('dashboard.sg', compact('kpis', 'graphes', 'papa', 'user'));
    }

    private function dashboardDirection(Papa $papa, $user)
    {
        $direction = $user->direction;
        if (!$direction) {
            return $this->dashboardGeneral($papa, $user);
        }
        $kpisDirection = $this->dashboardService->kpisDirection($papa, $direction);

        return view('dashboard.direction', compact('kpisDirection', 'papa', 'user', 'direction'));
    }

    private function dashboardService(Papa $papa, $user)
    {
        $direction = $user->direction;
        $kpisDirection = $direction
            ? $this->dashboardService->kpisDirection($papa, $direction)
            : [];

        return view('dashboard.service', compact('kpisDirection', 'papa', 'user', 'direction'));
    }

    private function dashboardAudit(Papa $papa, $user)
    {
        $kpis    = $this->dashboardService->kpisExecutif($papa);
        $graphes = [
            'activites'  => $this->dashboardService->repartitionActivitesStatut($papa),
            'evolution'  => $this->dashboardService->evolutionTrimestrielle($papa),
        ];

        return view('dashboard.audit', compact('kpis', 'graphes', 'papa', 'user'));
    }

    private function dashboardGeneral(Papa $papa, $user)
    {
        return view('dashboard.general', compact('papa', 'user'));
    }
}
