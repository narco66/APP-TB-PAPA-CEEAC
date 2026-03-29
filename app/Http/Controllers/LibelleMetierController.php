<?php

namespace App\Http\Controllers;

use App\Models\LibelleMetier;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LibelleMetierController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $this->authorize('parametres.libelles.voir');

        $query = LibelleMetier::orderBy('module')->orderBy('cle');

        if ($request->filled('module')) {
            $query->where('module', $request->string('module'));
        }

        $libelles = $query->get();
        $modules  = LibelleMetier::distinct()->pluck('module')->sort()->values();

        return view('parametres.libelles.index', compact('libelles', 'modules'));
    }

    public function update(Request $request, LibelleMetier $libelle)
    {
        $this->authorize('parametres.libelles.modifier');
        abort_if($libelle->est_systeme && !$request->user()->hasRole('super_admin'), 403, 'Ce libellé système ne peut être modifié que par un super administrateur.');

        $data = $request->validate([
            'valeur_courante' => 'nullable|string|max:300',
            'valeur_courte'   => 'nullable|string|max:100',
        ]);

        $avant = ['valeur_courante' => $libelle->valeur_courante, 'valeur_courte' => $libelle->valeur_courte];
        $libelle->update(array_merge($data, ['modifie_par' => $request->user()->id]));

        Cache::forget("libelle.{$libelle->cle}");

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'libelle_modifie',
            auditable: $request->user(),
            acteur: $request->user(),
            action: 'modifier',
            description: "Libellé {$libelle->cle} modifié",
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', "Libellé \"{$libelle->cle}\" mis à jour.");
    }

    public function reinitialiser(Request $request, string $module)
    {
        $this->authorize('parametres.libelles.modifier');

        $libelles = LibelleMetier::where('module', $module)->get();

        foreach ($libelles as $l) {
            $l->update(['valeur_courante' => null, 'valeur_courte' => null]);
            Cache::forget("libelle.{$l->cle}");
        }

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'libelle_reinitialise',
            auditable: $request->user(),
            acteur: $request->user(),
            action: 'reinitialiser',
            description: "Libellés du module '{$module}' réinitialisés aux valeurs par défaut",
        );

        return back()->with('success', "Libellés du module '{$module}' réinitialisés.");
    }
}
