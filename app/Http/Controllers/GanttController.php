<?php

namespace App\Http\Controllers;

use App\Exports\GanttExport;
use App\Http\Requests\GanttFilterRequest;
use App\Models\Activite;
use App\Models\Direction;
use App\Services\Gantt\GanttDataService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class GanttController extends Controller
{
    public function __construct(private GanttDataService $ganttService) {}

    // ── Vue ──────────────────────────────────────────────────────

    public function index(GanttFilterRequest $request)
    {
        $this->authorize('activite.voir');

        $user = $request->user();

        $directions = $user->can('activite.voir_toutes_directions')
            ? Direction::actif()->orderBy('libelle')->get(['id', 'libelle'])
            : collect();

        return view('gantt.index', [
            'scopeLabel' => $user->scopeLabel(),
            'directions' => $directions,
        ]);
    }

    // ── Données JSON ─────────────────────────────────────────────

    public function data(GanttFilterRequest $request): JsonResponse
    {
        $this->authorize('activite.voir');

        $data = $this->ganttService->build($request->user(), $request->validated());

        return response()->json($data);
    }

    // ── Détail d'une activité (panneau latéral) ──────────────────

    public function detail(Request $request, int $id): JsonResponse
    {
        $this->authorize('activite.voir');

        $user = $request->user();

        $activite = Activite::visibleTo($user)
            ->with([
                'resultatAttendu:id,code,libelle,objectif_immediat_id',
                'resultatAttendu.objectifImmediats:id,code,libelle,action_prioritaire_id',
                'resultatAttendu.objectifImmediats.actionPrioritaire:id,code,libelle',
                'direction:id,libelle',
                'service:id,libelle',
                'responsable:id,name',
                'pointFocal:id,name',
                'alertes' => fn($q) => $q->where('statut', 'nouvelle')->latest()->limit(5),
                'documents' => fn($q) => $q->latest()->limit(8),
            ])
            ->find($id);

        if (!$activite) {
            return response()->json(['error' => 'Activité non trouvée ou hors périmètre.'], 404);
        }

        $ra = $activite->resultatAttendu;
        $oi = $ra?->objectifImmediats;
        $ap = $oi?->actionPrioritaire;

        return response()->json([
            'id'               => $activite->id,
            'code'             => $activite->code,
            'libelle'          => $activite->libelle,
            'description'      => $activite->description,
            'statut'           => $activite->statut,
            'priorite'         => $activite->priorite,
            'est_jalon'        => $activite->est_jalon,
            'est_retard'       => $activite->estEnRetard(),
            'taux_realisation' => (float) $activite->taux_realisation,

            'date_debut_prevue'  => $activite->date_debut_prevue?->format('d/m/Y'),
            'date_fin_prevue'    => $activite->date_fin_prevue?->format('d/m/Y'),
            'date_debut_reelle'  => $activite->date_debut_reelle?->format('d/m/Y'),
            'date_fin_reelle'    => $activite->date_fin_reelle?->format('d/m/Y'),

            'budget_prevu'    => (float) $activite->budget_prevu,
            'budget_engage'   => (float) $activite->budget_engage,
            'budget_consomme' => (float) $activite->budget_consomme,
            'devise'          => $activite->devise,

            'direction'   => $activite->direction?->libelle,
            'service'     => $activite->service?->libelle,
            'responsable' => $activite->responsable?->name,
            'point_focal' => $activite->pointFocal?->name,

            'rbm' => [
                'resultat_attendu'   => $ra ? "[{$ra->code}] {$ra->libelle}"   : null,
                'objectif_immediat'  => $oi ? "[{$oi->code}] {$oi->libelle}"   : null,
                'action_prioritaire' => $ap ? "[{$ap->code}] {$ap->libelle}"   : null,
            ],

            'alertes' => $activite->alertes->map(fn($a) => [
                'titre'   => $a->titre,
                'niveau'  => $a->niveau,
                'message' => Str::limit($a->message, 120),
            ])->values(),

            'documents' => $activite->documents->map(fn($d) => [
                'nom'       => $d->nom_fichier_original ?? 'Document',
                'extension' => $d->extension ?? '',
            ])->values(),

            'url_detail' => route('activites.show', $activite->id),
        ]);
    }

    // ── Export Excel ─────────────────────────────────────────────

    public function exportExcel(GanttFilterRequest $request)
    {
        $this->authorize('activite.voir');

        $user    = $request->user();
        $filters = $request->validated();
        $data    = $this->ganttService->buildFresh($user, $filters);

        $filename = 'gantt-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new GanttExport(
                tasks:       $data['data'],
                scopeLabel:  $data['scope_label'],
                filters:     $filters,
                generatedBy: $user->name,
            ),
            $filename
        );
    }

    // ── Export PDF ───────────────────────────────────────────────

    public function exportPdf(GanttFilterRequest $request)
    {
        $this->authorize('activite.voir');

        $user    = $request->user();
        $filters = $request->validated();
        $data    = $this->ganttService->buildFresh($user, $filters);

        $filterParts = [];
        if (!empty($filters['statut']))    $filterParts[] = 'Statut=' . $filters['statut'];
        if (!empty($filters['priorite'])) $filterParts[] = 'Priorité=' . $filters['priorite'];
        if (!empty($filters['date_from'])) $filterParts[] = 'Du ' . $filters['date_from'];
        if (!empty($filters['date_to']))   $filterParts[] = 'Au ' . $filters['date_to'];

        $pdf = Pdf::loadView('gantt.export-pdf', [
            'tasks'          => $data['data'],
            'scopeLabel'     => $data['scope_label'],
            'filterLabel'    => $filterParts ? implode(' | ', $filterParts) : 'Fenêtre glissante',
            'totalActivites' => $data['total'],
            'generatedBy'    => $user->name,
        ])->setPaper('a4', 'landscape');

        $filename = 'gantt-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }
}
