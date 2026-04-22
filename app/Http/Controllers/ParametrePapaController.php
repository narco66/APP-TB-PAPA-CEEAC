<?php

namespace App\Http\Controllers;

use App\Models\Papa;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ParametrePapaController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $this->authorize('parametres.papa.voir');

        $papas = Papa::query()
            ->visibleTo($request->user())
            ->withCount(['risques', 'rapports'])
            ->with('creePar', 'validePar')
            ->whereNotIn('statut', ['archive'])
            ->orderByDesc('annee')
            ->get();

        $scopeLabel = $request->user()->scopeLabel();

        return view('parametres.papa.index', compact('papas', 'scopeLabel'));
    }

    public function activer(Request $request, Papa $papa)
    {
        $this->authorize('parametres.papa.modifier');
        $this->ensurePapaVisible($request, $papa);

        abort_if(
            ! in_array($papa->statut, ['valide', 'en_execution'], true),
            422,
            'Seul un PAPA valide peut etre defini comme actif.'
        );

        $avant = Papa::query()->where('statut', 'en_execution')->pluck('id')->toArray();

        Papa::query()->where('statut', 'en_execution')->update(['statut' => 'valide']);
        $papa->update(['statut' => 'en_execution']);

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'papa_actif_change',
            auditable: $papa,
            acteur: $request->user(),
            action: 'modifier',
            description: "PAPA actif change vers {$papa->code} ({$papa->annee})",
            donneesAvant: ['papa_ids_actifs' => $avant],
            donneesApres: ['papa_id_actif' => $papa->id],
            papa: $papa,
        );

        return back()->with('success', "PAPA {$papa->code} ({$papa->annee}) defini comme PAPA actif.");
    }

    public function verrouiller(Request $request, Papa $papa)
    {
        $this->authorize('parametres.papa.modifier');
        $this->ensurePapaVisible($request, $papa);
        abort_if($papa->est_verrouille, 422, 'Ce PAPA est deja verrouille.');

        $papa->update(['est_verrouille' => true]);

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'papa_verrouille',
            auditable: $papa,
            acteur: $request->user(),
            action: 'verrouiller',
            description: "PAPA {$papa->code} verrouille",
            papa: $papa,
        );

        return back()->with('success', "PAPA {$papa->code} verrouille.");
    }

    public function deverrouiller(Request $request, Papa $papa)
    {
        $this->authorize('parametres.papa.modifier');
        $this->ensurePapaVisible($request, $papa);

        $papa->update(['est_verrouille' => false]);

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'papa_deverrouille',
            auditable: $papa,
            acteur: $request->user(),
            action: 'modifier',
            description: "PAPA {$papa->code} deverrouille",
            papa: $papa,
        );

        return back()->with('success', "PAPA {$papa->code} deverrouille.");
    }

    public function archiver(Request $request, Papa $papa)
    {
        $this->authorize('parametres.papa.archiver');
        $this->ensurePapaVisible($request, $papa);

        $request->validate([
            'motif' => 'required|string|min:20|max:500',
            'confirmation' => 'required|in:ARCHIVER',
        ], [
            'confirmation.in' => 'Vous devez taper exactement "ARCHIVER" pour confirmer.',
        ]);

        $peutArchiver = in_array($papa->statut, ['cloture', 'valide'], true)
            || $request->user()->hasRole('super_admin');

        abort_unless($peutArchiver, 422, 'Seul un PAPA cloture peut etre archive, sauf super admin.');

        $papa->update([
            'statut' => 'archive',
            'archived_by' => $request->user()->id,
            'archived_at' => now(),
            'motif_archivage' => $request->motif,
            'est_verrouille' => true,
        ]);

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'papa_archive',
            auditable: $papa,
            acteur: $request->user(),
            action: 'archiver',
            description: "PAPA {$papa->code} archive. Motif : {$request->motif}",
            papa: $papa,
        );

        return redirect()
            ->route('parametres.papa.index')
            ->with('success', "PAPA {$papa->code} archive avec succes.");
    }

    public function archives(Request $request)
    {
        $this->authorize('parametres.papa.voir');

        $archives = Papa::query()
            ->visibleTo($request->user())
            ->where('statut', 'archive')
            ->with('archivePar', 'creePar')
            ->orderByDesc('archived_at')
            ->paginate(20);

        $scopeLabel = $request->user()->scopeLabel();

        return view('parametres.papa.archives', compact('archives', 'scopeLabel'));
    }

    private function ensurePapaVisible(Request $request, Papa $papa): void
    {
        abort_unless(
            Papa::query()->visibleTo($request->user())->whereKey($papa->id)->exists(),
            403
        );
    }
}
