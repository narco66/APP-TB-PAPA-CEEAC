<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use App\Services\ParametreService;
use Illuminate\Http\Request;

class ConfigRbmController extends Controller
{
    public function __construct(
        private ParametreService $parametreService,
        private AuditService $auditService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('parametres.rbm.voir');

        $config = $this->parametreService->getGroupe('rbm');
        $scopeLabel = $this->scopeLabel($request);

        return view('parametres.rbm.index', compact('config', 'scopeLabel'));
    }

    public function save(Request $request)
    {
        $this->authorize('parametres.rbm.modifier');
        $this->ensureGlobalScope($request);

        $data = $request->validate([
            'rbm_seuil_atteint' => 'required|integer|min:1|max:100',
            'rbm_seuil_risque' => 'required|integer|min:1|max:100',
            'rbm_seuil_non_atteint' => 'required|integer|min:1|max:100',
            'rbm_prefixe_ap' => 'required|string|max:10',
            'rbm_prefixe_oi' => 'required|string|max:10',
            'rbm_prefixe_ra' => 'required|string|max:10',
        ]);

        abort_if(
            $data['rbm_seuil_non_atteint'] >= $data['rbm_seuil_risque'],
            422,
            'Le seuil "non atteint" doit etre inferieur au seuil "en risque".'
        );
        abort_if(
            $data['rbm_seuil_risque'] >= $data['rbm_seuil_atteint'],
            422,
            'Le seuil "en risque" doit etre inferieur au seuil "atteint".'
        );

        $avant = $this->parametreService->getGroupe('rbm');
        $this->parametreService->saveGroupe('rbm', $data, $request->user());

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'config_rbm_modifiee',
            auditable: null,
            acteur: $request->user(),
            action: 'modifier',
            description: 'Configuration RBM modifiee',
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', 'Configuration RBM sauvegardee.');
    }

    private function ensureGlobalScope(Request $request): void
    {
        abort_unless($request->user()->resolveVisibilityScope()->isGlobal, 403);
    }

    private function scopeLabel(Request $request): string
    {
        return $request->user()->resolveVisibilityScope()->isGlobal
            ? 'Perimetre de donnees : Consolidation institutionnelle'
            : $request->user()->scopeLabel();
    }
}
