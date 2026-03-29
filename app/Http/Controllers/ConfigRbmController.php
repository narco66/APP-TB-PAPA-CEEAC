<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use App\Services\AuditService;
use App\Services\ParametreService;
use Illuminate\Http\Request;

class ConfigRbmController extends Controller
{
    public function __construct(
        private ParametreService $parametreService,
        private AuditService $auditService,
    ) {}

    public function index()
    {
        $this->authorize('parametres.rbm.voir');
        $config = $this->parametreService->getGroupe('rbm');
        return view('parametres.rbm.index', compact('config'));
    }

    public function save(Request $request)
    {
        $this->authorize('parametres.rbm.modifier');

        $data = $request->validate([
            'rbm_seuil_atteint'     => 'required|integer|min:1|max:100',
            'rbm_seuil_risque'      => 'required|integer|min:1|max:100',
            'rbm_seuil_non_atteint' => 'required|integer|min:1|max:100',
            'rbm_prefixe_ap'        => 'required|string|max:10',
            'rbm_prefixe_oi'        => 'required|string|max:10',
            'rbm_prefixe_ra'        => 'required|string|max:10',
        ]);

        // Validate ordering: non_atteint < risque < atteint
        abort_if(
            $data['rbm_seuil_non_atteint'] >= $data['rbm_seuil_risque'],
            422,
            'Le seuil "non atteint" doit être inférieur au seuil "en risque".'
        );
        abort_if(
            $data['rbm_seuil_risque'] >= $data['rbm_seuil_atteint'],
            422,
            'Le seuil "en risque" doit être inférieur au seuil "atteint".'
        );

        $avant = $this->parametreService->getGroupe('rbm');
        $this->parametreService->saveGroupe('rbm', $data, $request->user());

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'config_rbm_modifiee',
            auditable: null,
            acteur: $request->user(),
            action: 'modifier',
            description: 'Configuration RBM modifiée',
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', 'Configuration RBM sauvegardée.');
    }
}
