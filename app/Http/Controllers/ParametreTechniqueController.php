<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use App\Services\ParametreService;
use Illuminate\Http\Request;

class ParametreTechniqueController extends Controller
{
    public function __construct(
        private ParametreService $parametreService,
        private AuditService $auditService,
    ) {}

    public function index()
    {
        $this->authorize('parametres.technique.voir');

        $config          = $this->parametreService->getGroupe('technique');
        $config_ged      = $this->parametreService->getGroupe('ged');
        $config_affichage = $this->parametreService->getGroupe('affichage');

        // System info
        $info = [
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'env'             => app()->environment(),
            'debug'           => config('app.debug'),
            'timezone'        => config('app.timezone'),
            'cache_driver'    => config('cache.default'),
            'queue_driver'    => config('queue.default'),
        ];

        return view('parametres.technique.index', compact('config', 'config_ged', 'config_affichage', 'info'));
    }

    public function save(Request $request)
    {
        $this->authorize('parametres.technique.modifier');

        $data = $request->validate([
            'session_duree_minutes'    => 'required|integer|min:5|max:1440',
            'upload_taille_max_mo'     => 'required|integer|min:1|max:100',
            'upload_formats_autorises' => 'required|string|max:200',
            'pagination_items'         => 'required|integer|min:5|max:200',
            'export_format_defaut'     => 'required|in:xlsx,csv,pdf',
        ]);

        $avant = array_merge(
            $this->parametreService->getGroupe('technique'),
            $this->parametreService->getGroupe('ged'),
            $this->parametreService->getGroupe('affichage'),
        );

        // Save to appropriate groups
        $this->parametreService->set('session_duree_minutes', $data['session_duree_minutes'], $request->user());
        $this->parametreService->set('upload_taille_max_mo', $data['upload_taille_max_mo'], $request->user());
        $this->parametreService->set('upload_formats_autorises', $data['upload_formats_autorises'], $request->user());
        $this->parametreService->set('pagination_items', $data['pagination_items'], $request->user());
        $this->parametreService->set('export_format_defaut', $data['export_format_defaut'], $request->user());

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'parametres_techniques_modifies',
            auditable: null,
            acteur: $request->user(),
            action: 'modifier',
            description: 'Paramètres techniques modifiés',
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', 'Paramètres techniques sauvegardés.');
    }
}
