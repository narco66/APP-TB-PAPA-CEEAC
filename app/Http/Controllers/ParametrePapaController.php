<?php

namespace App\Http\Controllers;

use App\Models\Papa;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ParametrePapaController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index()
    {
        $this->authorize('parametres.papa.voir');

        $papas = Papa::withCount(['risques', 'rapports'])
            ->with('creePar', 'validePar')
            ->whereNotIn('statut', ['archive'])
            ->orderByDesc('annee')
            ->get();

        return view('parametres.papa.index', compact('papas'));
    }

    public function activer(Request $request, Papa $papa)
    {
        $this->authorize('parametres.papa.modifier');

        abort_if(
            !in_array($papa->statut, ['valide', 'en_execution']),
            422,
            'Seul un PAPA validé peut être défini comme actif.'
        );

        $avant = Papa::where('statut', 'en_execution')->pluck('id')->toArray();

        Papa::where('statut', 'en_execution')->update(['statut' => 'valide']);
        $papa->update(['statut' => 'en_execution']);

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'papa_actif_change',
            auditable: $papa,
            acteur: $request->user(),
            action: 'modifier',
            description: "PAPA actif changé vers {$papa->code} ({$papa->annee})",
            donneesAvant: ['papa_ids_actifs' => $avant],
            donneesApres: ['papa_id_actif' => $papa->id],
            papa: $papa,
        );

        return back()->with('success', "PAPA {$papa->code} ({$papa->annee}) défini comme PAPA actif en exécution.");
    }

    public function verrouiller(Request $request, Papa $papa)
    {
        $this->authorize('parametres.papa.modifier');
        abort_if($papa->est_verrouille, 422, 'Ce PAPA est déjà verrouillé.');

        $papa->update(['est_verrouille' => true]);

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'papa_verrouille',
            auditable: $papa,
            acteur: $request->user(),
            action: 'verrouiller',
            description: "PAPA {$papa->code} verrouillé",
            papa: $papa,
        );

        return back()->with('success', "PAPA {$papa->code} verrouillé. Il ne peut plus être modifié.");
    }

    public function deverrouiller(Request $request, Papa $papa)
    {
        $this->authorize('parametres.papa.modifier');

        $papa->update(['est_verrouille' => false]);

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'papa_deverrouille',
            auditable: $papa,
            acteur: $request->user(),
            action: 'modifier',
            description: "PAPA {$papa->code} déverrouillé",
            papa: $papa,
        );

        return back()->with('success', "PAPA {$papa->code} déverrouillé.");
    }

    public function archiver(Request $request, Papa $papa)
    {
        $this->authorize('parametres.papa.archiver');

        $request->validate([
            'motif'        => 'required|string|min:20|max:500',
            'confirmation' => 'required|in:ARCHIVER',
        ], [
            'confirmation.in' => 'Vous devez taper exactement "ARCHIVER" pour confirmer.',
        ]);

        $peutArchiver = in_array($papa->statut, ['cloture', 'valide'])
            || $request->user()->hasRole('super_admin');

        abort_unless($peutArchiver, 422, 'Seul un PAPA clôturé peut être archivé (ou super admin).');

        $papa->update([
            'statut'          => 'archive',
            'archived_by'     => $request->user()->id,
            'archived_at'     => now(),
            'motif_archivage' => $request->motif,
            'est_verrouille'  => true,
        ]);

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'papa_archive',
            auditable: $papa,
            acteur: $request->user(),
            action: 'archiver',
            description: "PAPA {$papa->code} archivé. Motif : {$request->motif}",
            papa: $papa,
        );

        return redirect()
            ->route('parametres.papa.index')
            ->with('success', "PAPA {$papa->code} archivé avec succès.");
    }

    public function archives()
    {
        $this->authorize('parametres.papa.voir');

        $archives = Papa::where('statut', 'archive')
            ->with('archivePar', 'creePar')
            ->orderByDesc('archived_at')
            ->paginate(20);

        return view('parametres.papa.archives', compact('archives'));
    }
}
