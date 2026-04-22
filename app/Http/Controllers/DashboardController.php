<?php

namespace App\Http\Controllers;

use App\Models\Papa;
use App\Services\AlerteService;
use App\Services\DashboardService;
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

        $papa = Papa::enExecution()->orderByDesc('annee')->first()
            ?? Papa::orderByDesc('annee')->first();

        if (! $papa) {
            return view('dashboard.vide', compact('user'));
        }

        $rolePrincipal = $user->getRoleNames()->first();

        return match ($rolePrincipal) {
            'president', 'super_admin' => $this->dashboardPresident($papa, $user),
            'vice_president' => $this->dashboardVicePresident($papa, $user),
            'commissaire' => $this->dashboardCommissaire($papa, $user),
            'secretaire_general' => $this->dashboardSG($papa, $user),
            'directeur_technique', 'directeur_appui' => $this->dashboardDirection($papa, $user),
            'chef_service', 'point_focal' => $this->dashboardServiceLevel($papa, $user),
            'auditeur_interne', 'controle_financier' => $this->dashboardAudit($papa, $user),
            default => $this->dashboardGeneral($papa, $user),
        };
    }

    private function dashboardPresident(Papa $papa, $user)
    {
        $scopeLabel = $user->scopeLabel();
        $kpis = $this->dashboardService->kpisExecutif($papa, $user);
        $graphes = [
            'evolution' => $this->dashboardService->evolutionTrimestrielle($papa, $user),
            'departements' => $this->dashboardService->comparatifDepartements($papa, $user),
            'activites' => $this->dashboardService->repartitionActivitesStatut($papa, $user),
        ];
        $alertes = $this->alerteService->compterParNiveau($papa);
        $actionsPrioritaires = $papa->actionsPrioritaires()->visibleTo($user)->with('departement')->orderBy('ordre')->take(10)->get();

        return view('dashboard.president', compact('kpis', 'graphes', 'alertes', 'papa', 'user', 'scopeLabel', 'actionsPrioritaires'));
    }

    private function dashboardVicePresident(Papa $papa, $user)
    {
        $scopeLabel = $user->scopeLabel();
        $kpis = $this->dashboardService->kpisExecutif($papa, $user);
        $graphes = [
            'evolution' => $this->dashboardService->evolutionTrimestrielle($papa, $user),
            'departements' => $this->dashboardService->comparatifDepartements($papa, $user),
        ];

        return view('dashboard.vice_president', compact('kpis', 'graphes', 'papa', 'user', 'scopeLabel'));
    }

    private function dashboardCommissaire(Papa $papa, $user)
    {
        $departement = $user->direction?->departement ?? $user->departement;
        $scopeLabel = $user->scopeLabel();
        $kpisGlobaux = $this->dashboardService->kpisExecutif($papa, $user);

        return view('dashboard.commissaire', compact('kpisGlobaux', 'papa', 'user', 'departement', 'scopeLabel'));
    }

    private function dashboardSG(Papa $papa, $user)
    {
        $scopeLabel = $user->scopeLabel();
        $kpis = $this->dashboardService->kpisExecutif($papa, $user);
        $graphes = [
            'evolution' => $this->dashboardService->evolutionTrimestrielle($papa, $user),
            'departements' => $this->dashboardService->comparatifDepartements($papa, $user),
            'activites' => $this->dashboardService->repartitionActivitesStatut($papa, $user),
        ];

        return view('dashboard.sg', compact('kpis', 'graphes', 'papa', 'user', 'scopeLabel'));
    }

    private function dashboardDirection(Papa $papa, $user)
    {
        $direction = $user->direction;
        if (! $direction) {
            return $this->dashboardGeneral($papa, $user);
        }

        $scopeLabel = $user->scopeLabel();
        $kpisDirection = $this->dashboardService->kpisDirection($papa, $direction, $user);

        return view('dashboard.direction', compact('kpisDirection', 'papa', 'user', 'direction', 'scopeLabel'));
    }

    private function dashboardServiceLevel(Papa $papa, $user)
    {
        $direction = $user->direction;
        $scopeLabel = $user->scopeLabel();
        $kpisDirection = $direction
            ? $this->dashboardService->kpisDirection($papa, $direction, $user)
            : [];

        return view('dashboard.service', compact('kpisDirection', 'papa', 'user', 'direction', 'scopeLabel'));
    }

    private function dashboardAudit(Papa $papa, $user)
    {
        $scopeLabel = $user->scopeLabel();
        $kpis = $this->dashboardService->kpisExecutif($papa, $user);
        $graphes = [
            'activites' => $this->dashboardService->repartitionActivitesStatut($papa, $user),
            'evolution' => $this->dashboardService->evolutionTrimestrielle($papa, $user),
        ];

        return view('dashboard.audit', compact('kpis', 'graphes', 'papa', 'user', 'scopeLabel'));
    }

    private function dashboardGeneral(Papa $papa, $user)
    {
        $scopeLabel = $user->scopeLabel();

        return view('dashboard.general', compact('papa', 'user', 'scopeLabel'));
    }
}
